import DocumentService from"@typo3/core/document-service.js";

class AIModule {
    constructor() {
        this.aiButton = document.getElementById('ai-ask-button');
        this.responseElement = document.getElementById('ai-response');
        this.message = document.getElementById('ai-question');
        if (this.aiButton && this.responseElement && this.message) {
            this.aiButton.addEventListener('click', async () => {
                await this.clickAction();
            });
        } else {
            console.error('Required elements are not found in the DOM.');
        }
    }

    async clickAction() {
        this.responseElement.textContent = 'Loading...';

        try {
            const response = await fetch(TYPO3.settings.ajaxUrls.error_log_ai_ask, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(
                    {
                        message: this.message.value,
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
    }
}

DocumentService.ready().then(() => {
    new AIModule();
});
