import DateTimePicker from"@typo3/backend/date-time-picker.js"
import DocumentService from"@typo3/core/document-service.js";
class ListModule {
    constructor() {
        this.dateTimePickerElements = null;
            DocumentService.ready().then((() => {
                this.dateTimePickerElements = document.querySelectorAll(".t3js-datetimepicker");
                this.initializeDateTimePickerElements();
        }))
    }
    initializeDateTimePickerElements() {
        this.dateTimePickerElements.forEach((e => DateTimePicker.initialize(e)))
    }
}

export default new ListModule();