<?php

declare(strict_types=1);

namespace RD\ErrorLog\Backend\Controller;

use Psr\Http\Message\ResponseInterface;
use RD\ErrorLog\Domain\Enum\Frequency;
use RD\ErrorLog\Domain\Enum\Model;
use RD\ErrorLog\Domain\Enum\Option;
use RD\ErrorLog\Domain\Model\Filter;
use RD\ErrorLog\Domain\Model\Settings;
use RD\ErrorLog\Domain\Repository\ErrorRepository;
use RD\ErrorLog\Domain\Repository\SettingsRepository;
use RD\ErrorLog\Domain\Repository\BackendUserRepository;
use RD\ErrorLog\Service\ConfigurationService;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Belog\Domain\Repository\LogEntryRepository;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class LogErrorModuleController extends ActionController
{
    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        protected readonly LogEntryRepository $logEntryRepository,
        protected readonly BackendUriBuilder $backendUriBuilder,
        protected readonly IconFactory $iconFactory,
        protected readonly PageRenderer $pageRenderer,
        protected readonly ErrorRepository $errorRepository,
        protected readonly SettingsRepository $settingsRepository,
        protected readonly BackendUserRepository $backendUserRepository,
        protected readonly ConfigurationService $configurationService
    ) {
    }


    public function initializeAction(): void
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
    }

    public function indexAction(Filter $filter = null, int $currentPage = 1, string $operation = ''): ResponseInterface
    {
        if ($filter === null || $operation === 'reset') {
            $filter = new Filter();
        }

        $this->pageRenderer->loadJavaScriptModule('@rd/error-log/ListModule.js');

        $errors = $this->errorRepository->getErrors($filter);
        $maxOptions = [
            '25' => '25',
            '50' => '50',
            '100' => '100',
            '200' => '200',
        ];
        $rootPagesUid = $this->errorRepository->getRootPages();

        $rootPages = [0 => 'All'];
        foreach ($rootPagesUid as $page) {
            $rootPages[] = $page['root_page_uid'];
        }

        $paginator = new ArrayPaginator($errors, $currentPage, $filter->getLimit());
        $pagination = new SimplePagination($paginator);
        $viewVariables = [
            'paginator' => $paginator,
            'pagination' => $pagination,
            'currentPage' => $currentPage,
            'maxOptions' => $maxOptions,
            'rootPages' => $rootPages,
            'filter' => $filter,
        ];
        $this->addMainMenu('index');

        return $this->moduleTemplate
            ->setFlashMessageQueue($this->getFlashMessageQueue())
            ->setTitle($this->translate('errors_log'))
            ->assignMultiple($viewVariables)
            ->renderResponse('LogErrorModule/Index');
    }

    /**
     * Delete records from the error log.
     *
     * @param int $uid The ID of the error entry to delete
     */
    public function deleteAction(int $uid): ResponseInterface
    {
        $this->errorRepository->deleteByUid($uid);
        $this->addFlashMessage($this->translate('messages.errors_deleted'), '', ContextualFeedbackSeverity::INFO);
        return $this->redirect('index');
    }

    public function viewAction(int $uid, int $currentPage = 1, int $limit = 10)
    {
        $errors = $this->errorRepository->getErrorsByUid($uid);

        $paginator = new ArrayPaginator($errors, $currentPage, $limit);
        $pagination = new SimplePagination($paginator);

        $settings = $this->settingsRepository->getSettings();

        if ($errors === null) {
            $this->addFlashMessage(
                $this->translate('messages.log_not_found'),
                '',
                ContextualFeedbackSeverity::ERROR
            );
            $this->redirect('index');
        }
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $backButton = $buttonBar->makeLinkButton()
            ->setIcon($this->iconFactory->getIcon('actions-view-go-back', Icon::SIZE_SMALL))
            ->setTitle($this->translate('errors.labels.goBack'))
            ->setHref($this->uriBuilder->uriFor('index'));
        $buttonBar->addButton($backButton);
        $deleteButton = $buttonBar->makeLinkButton()
            ->setIcon($this->iconFactory->getIcon('actions-edit-delete', Icon::SIZE_SMALL))
            ->setTitle($this->translate('errors.labels.delete'))
            ->setHref($this->uriBuilder->uriFor('delete', ['uid' => $uid]));

        $buttonBar->addButton($deleteButton, ButtonBar::BUTTON_POSITION_RIGHT);
        $this->pageRenderer->addCssFile('EXT:error_log/Resources/Public/Css/Backend.css');

        $this->pageRenderer->loadJavaScriptModule('@rd/error-log/Contrib/json.js');
        $this->pageRenderer->loadJavaScriptModule('@rd/error-log/AIModule.js');
        $this->pageRenderer->loadJavaScriptModule('@rd/error-log/LogError.js');

        $viewVariables = [
            'paginator' => $paginator,
            'pagination' => $pagination,
            'currentPage' => $currentPage,
            'settings' => $settings,
            'error' => $errors[0],
            'AIPrompt' => $this->buildAIPrompt($errors[0]),
        ];

        return $this->moduleTemplate
            ->setFlashMessageQueue($this->getFlashMessageQueue())
            ->setTitle($this->translate('errors_log'))
            ->assignMultiple($viewVariables)
            ->renderResponse('LogErrorModule/View');
    }

    private function buildAIPrompt(array $error): string
    {
        $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
        $typo3Version = $typo3Version->getVersion();
        $AIPrompt = "I am encountering an error in TYPO3 CMS and need assistance in understanding and resolving it. Below are the details of the error I am facing:\n";
        $AIPrompt .= "- Error message: " . $error['message'] . "\n";
        if ($error['code'] !== 0) {
            $AIPrompt .= "- Error code: " . $error['code'] . "\n";
        }
        $AIPrompt .= "- TYPO3 version:" . $typo3Version . "\n\n";
        $AIPrompt .= "Requirements:\n";
        $AIPrompt .= "1. Description of the Error: Provide a detailed explanation of what this error message means.\n";
        $AIPrompt .= "2. Possible Causes: List potential reasons why this error could occur within the TYPO3 CMS context.\n";
        $AIPrompt .= "3. Suggested Fixes: Offer step-by-step guidance on how to resolve this error, including any recommended changes with code snippet examples. In your suggestions always use the latest possible approach available for target TYPO3 version.";

        return $AIPrompt;
    }

    public function settingsAction(): ResponseInterface
    {
        $message = '';
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $backButton = $buttonBar->makeLinkButton()
            ->setIcon($this->iconFactory->getIcon('actions-view-go-back', Icon::SIZE_SMALL))
            ->setTitle($this->translate('errors.labels.goBack'))
            ->setHref($this->uriBuilder->uriFor('index'));
        $buttonBar->addButton($backButton);

        $settings = $this->settingsRepository->getSettings();
        $handlersSet = $this->configurationService->checkAreHandlersIsSet();

        if ($settings->getPrePrompt() === '') {
            $settings->setPrePrompt($this->translate('ai.preprompt_text'));
        }

        if ($settings->getGeneralEnable() && !$handlersSet) {
            $message = $this->translate('messages.handlers_not_set_extension_will_not_work_correctly');
        }

        $users = $this->backendUserRepository->getUsersWithEnabledErrorsNotifications();

        $this->pageRenderer->loadJavaScriptModule('@rd/error-log/AITestModule.js');
        $this->pageRenderer->loadJavaScriptModule('@rd/error-log/SlackTestModule.js');

        $viewVariables = [
            'message' => $message,
            'settings' => $settings,
            'users' => $users,
            'occurrenceOptions' => $this->translateOptions(Option::getOptions()),
            'errorReportsOptions' => $this->translateOptions(Frequency::getOptions()),
            'modelOptions' => $this->translateOptions(Model::getOptions()),
        ];

        $this->addMainMenu('settings');

        return $this->moduleTemplate
            ->setFlashMessageQueue($this->getFlashMessageQueue())
            ->setTitle($this->translate('errors_log'))
            ->assignMultiple($viewVariables)
            ->renderResponse('LogErrorModule/Settings');
    }

    public function saveSettingsAction(Settings $settings): ResponseInterface
    {
        $arguments = $this->request->getArguments();
        $this->settingsRepository->update($settings);
        if ($settings->getGeneralEnable()) {
            $this->configurationService->modifyHandlers(true);
        }
        $this->addFlashMessage($this->translate('messages.settings_saved'), '', ContextualFeedbackSeverity::INFO);
        return $this->redirect('settings', null, null, $arguments);
    }

    protected function addMainMenu(string $currentAction): void
    {
        $this->uriBuilder->setRequest($this->request);
        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('ErrorLogModuleMenu');
        $menu->addMenuItem(
            $menu->makeMenuItem()
                ->setTitle($this->translate('errors_log'))
                ->setHref($this->uriBuilder->uriFor('index'))
                ->setActive($currentAction === 'index')
        );
        $menu->addMenuItem(
            $menu->makeMenuItem()
                ->setTitle($this->translate('settings'))
                ->setHref($this->uriBuilder->uriFor('settings'))
                ->setActive($currentAction === 'settings')
        );
        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    private function translateOptions(array $options): array
    {
        foreach ($options as $key => $value) {
            $options[$key] = $this->translate($value);
        }

        return $options;
    }

    private function translate(string $key, string $extensionKey = 'error_log'): string
    {
        return LocalizationUtility::translate('LLL:EXT:error_log/Resources/Private/Language/locallang.xlf:' . $key, $extensionKey) ?? '';
    }
}
