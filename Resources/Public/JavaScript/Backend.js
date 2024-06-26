document.querySelectorAll('.error-list__item').forEach(function(item) {
    item.addEventListener('click', function() {
        const jsonViewer = document.createElement("andypf-json-viewer")
        jsonViewer.id = "json"
        jsonViewer.expanded = 2
        jsonViewer.indent = 2
        jsonViewer.showDataTypes = true
        jsonViewer.theme = "classic-light"
        jsonViewer.showToolbar = false
        jsonViewer.showSize = true
        jsonViewer.showCopy = true
        jsonViewer.expandIconType = "square"
        jsonViewer.data = item.querySelector('.error-list__item__text__additional').innerHTML

        let information = item.parentElement.parentElement.querySelector('.additional-information');
        if (information.firstChild) {
            information.replaceChild(jsonViewer, information.firstChild);
        } else {
            information.appendChild(jsonViewer);
        }

        document.querySelectorAll('.error-list__item').forEach(function(item) {
            item.classList.remove('active');
        });
        item.classList.toggle('active');
    });
});
const resizers = document.querySelectorAll('.resizer');

resizers.forEach(resizer => {
    resizer.addEventListener('mousedown', function(e) {
        e.preventDefault();
        const prevBlock = resizer.previousElementSibling;
        const nextBlock = resizer.nextElementSibling;
        const startX = e.clientX;
        const prevBlockWidth = prevBlock.getBoundingClientRect().width;
        const nextBlockWidth = nextBlock.getBoundingClientRect().width;

        function onMouseMove(e) {
            const dx = e.clientX - startX;
            prevBlock.style.width = `${prevBlockWidth + dx}px`;
            nextBlock.style.width = `${nextBlockWidth - dx}px`;
        }

        function onMouseUp() {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        }

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    });

    const copyIcons = document.querySelectorAll('.error-list__item__copy');

    copyIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const parentItem = icon.closest('.error-list__item');
            const heading = parentItem.querySelector('.error-list__item__text__heading').innerText;
            copyToClipboard(heading);
        });
    });

    function copyToClipboard(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
    }
});
