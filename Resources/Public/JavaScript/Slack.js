
const responseElement = document.getElementById('slack-test-response');
document.getElementById('slack-test-button').addEventListener('click', async function () {
    responseElement.textContent = 'Loading...';
    const message = this.dataset.message;
    const token = document.querySelector('input[name="tx_errorlog_system_errorlogtxerrorlog[settings][slackAuthToken]"]');
    const channelId = document.querySelector('input[name="tx_errorlog_system_errorlogtxerrorlog[settings][slackChannelId]"]');

    try {
        const response = await fetch(TYPO3.settings.ajaxUrls.error_log_slack_test, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(
                {
                    message: message,
                    token: token.value,
                    channelId: channelId.value,
                })
        });

        const data = await response.json();

        if (response.ok) {
            responseElement.innerHTML = data.message;
        } else {
            responseElement.textContent = `Error: ${data.error} response code: ${response.status}`;
        }
    } catch (error) {
        responseElement.textContent = `Error: ${error.message}`;
    }
});
