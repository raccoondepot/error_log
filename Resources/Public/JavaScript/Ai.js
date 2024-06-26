document.addEventListener('DOMContentLoaded', function () {
    const aiButton = document.getElementById('ai-button');
    const aiAskButton = document.getElementById('ai-ask-button');
    const responseElement = document.getElementById('ai-response');

    if (aiButton)
    aiButton.addEventListener('click', async function () {
        responseElement.textContent = 'Loading...';
        const userMessage = this.dataset.message;
        const openAITokenInput = document.querySelector('input[name="tx_errorlog_system_errorlogtxerrorlog[settings][openaiAuthToken]"]');
        const openAIModelInput = document.querySelector('select[name="tx_errorlog_system_errorlogtxerrorlog[settings][openaiModel]"]');
        const prePromptInput = document.querySelector('textarea[name="tx_errorlog_system_errorlogtxerrorlog[settings][prePrompt]"]');

        try {
            const response = await fetch(TYPO3.settings.ajaxUrls.error_log_ai_test, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(
                    {
                        message: userMessage,
                        token: openAITokenInput.value,
                        model: openAIModelInput.value,
                        prePrompt: prePromptInput.value,
                    })
            });

            const data = await response.json();
            if (response.ok) {
                responseElement.innerHTML = data.choices[0].message.content;
            } else {
                responseElement.textContent = `Error: ${data.error} response code: ${response.status}`;
            }
        } catch (error) {
            responseElement.textContent = `Error: ${error.message}`;
        }
    });

    if (aiAskButton)
    aiAskButton.addEventListener('click', async function () {
        responseElement.textContent = 'Loading...';
        const userMessage = document.getElementById('ai-question').value

        try {
            const response = await fetch(TYPO3.settings.ajaxUrls.error_log_ai_ask, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(
                    {
                        message: userMessage,
                    })
            });

            const data = await response.json();
            if (response.ok) {
                responseElement.innerHTML = data.message;
                aiAskButton.textContent = this.dataset.caption;
                hljs.highlightAll();
            } else {
                responseElement.textContent = `Error: ${data.error} response code: ${response.status}`;
            }
        } catch (error) {
            responseElement.textContent = `Error: ${error.message}`;
        }
    });
});
