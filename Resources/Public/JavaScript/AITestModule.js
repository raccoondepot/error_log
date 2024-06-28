import DocumentService from"@typo3/core/document-service.js";
class AITestModule {
    constructor() {
        this.aiButton = document.getElementById('ai-button');
        this.aiAskButton = document.getElementById('ai-ask-button');
        this.responseElement = document.getElementById('ai-response');
        this.initializeAIButton();
        this.initializeAIAskButton();
    }

    initializeAIButton() {
        if (this.aiButton) {
            this.aiButton.addEventListener('click', async () => {
                this.responseElement.textContent = 'Loading...';
                const userMessage = this.aiButton.dataset.message;
                const openAITokenInput = document.querySelector('input[name="settings[openaiAuthToken]"]');
                const openAIModelInput = document.querySelector('select[name="settings[openaiModel]"]');
                const prePromptInput = document.querySelector('textarea[name="settings[prePrompt]"]');

                try {
                    const response = await fetch(TYPO3.settings.ajaxUrls.error_log_ai_test, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            message: userMessage,
                            token: openAITokenInput.value,
                            model: openAIModelInput.value,
                            prePrompt: prePromptInput.value,
                        })
                    });

                    const data = await response.json();
                    console.log(data);
                    if (response.ok) {
                        this.responseElement.innerHTML = data.choices[0].message.content;
                    } else {
                        this.responseElement.textContent = `Error: ${data.error} response code: ${response.status}`;
                    }
                } catch (error) {
                    this.responseElement.textContent = `Error: ${error.message}`;
                }
            });
        }
    }

    initializeAIAskButton() {
        if (this.aiAskButton) {
            this.aiAskButton.addEventListener('click', async () => {
                this.responseElement.textContent = 'Loading...';
                const userMessage = document.getElementById('ai-question').value;

                try {
                    const response = await fetch(TYPO3.settings.ajaxUrls.error_log_ai_ask, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({message: userMessage})
                    });

                    const data = await response.json();
                    if (response.ok) {
                        this.responseElement.innerHTML = data.message;
                        this.aiAskButton.textContent = this.aiAskButton.dataset.caption;
                        hljs.highlightAll();
                    } else {
                        this.responseElement.textContent = `Error: ${data.error} response code: ${response.status}`;
                    }
                } catch (error) {
                    this.responseElement.textContent = `Error: ${error.message}`;
                }
            });
        }
    }
}
DocumentService.ready().then(() => {
    new AITestModule();
});
