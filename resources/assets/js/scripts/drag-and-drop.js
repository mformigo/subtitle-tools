/**
 * This code adds drag & drop functionality to the tool-form component.
 */
document.addEventListener('DOMContentLoaded', function () {
    var dropContainer = document.getElementById("drop-container");

    if (! dropContainer) {
        return;
    }

    var subtitlesInput = document.getElementById("subtitles-input");

    // The sub/idx tool uses different inputs, and should have
    // the default drag and drop behaviour disabled.
    var defaultDropInputExists = !! subtitlesInput;

    var dragLeaveTimeout = null;

    // Check if we are on IE11. Dragging and dropping doesn't work in IE11
    if (!! window.MSInputMethodContext && !! document.documentMode) {
        return;
    }

    function isDraggingFiles(dragEvent) {
        types = dragEvent.dataTransfer.types;

        return types && types.indexOf
            ? types.indexOf('Files') !== -1
            : types.contains('Files');
    }

    dropContainer.ondragleave = function (event) {
        clearTimeout(dragLeaveTimeout);

        dragLeaveTimeout = window.setTimeout(function () {
            dropContainer.classList.remove("dropzone-active");
        }, 50);
    };

    dropContainer.ondragover = function (event) {
        event.preventDefault();

        event.dataTransfer.dropEffect = 'copy';

        clearTimeout(dragLeaveTimeout);
    };

    if (! defaultDropInputExists) {
        return;
    }

    dropContainer.ondragenter = function (event) {
        event.preventDefault();

        if (isDraggingFiles(event)) {
            dropContainer.classList.add("dropzone-active");
        }
    };

    dropContainer.ondrop = function (event) {
        event.preventDefault();
        event.stopPropagation();

        dropContainer.classList.remove("dropzone-active");

        if (! isDraggingFiles(event)) {
            return;
        }

        if (! subtitlesInput.multiple && event.dataTransfer.files.length > 1) {
            subtitlesInput.value = '';
        } else {
            subtitlesInput.files = event.dataTransfer.files;
        }
    };

}, false);
