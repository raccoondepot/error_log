import DocumentService from"@typo3/core/document-service.js";
class SlackTestModule {
    constructor() {
        this.responseElement = document.getElementById('slack-test-response');
        this.testButton = document.getElementById('slack-test-button');
        this.initializeTestButton();
    }

    initializeTestButton() {
        this.testButton.addEventListener('click', async () => {
            this.responseElement.textContent = 'Loading...';
            const message = this.testButton.dataset.message;
            const tokenInput = document.querySelector('input[name="settings[slackAuthToken]"]');
            const channelIdInput = document.querySelector('input[name="settings[slackChannelId]"]');
            try {
                const response = await fetch(TYPO3.settings.ajaxUrls.error_log_slack_test, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message,
                        token: tokenInput.value,
                        channelId: channelIdInput.value,
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    this.responseElement.innerHTML = data.message;
                } else {
                    this.responseElement.textContent = `Error: ${data.error} response code: ${response.status}`;
                }
            } catch (error) {
                this.responseElement.textContent = `Error: ${error.message}`;
            }
        });
    }
}

DocumentService.ready().then(() => {
    new SlackTestModule();
});
