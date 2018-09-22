/**
 * This code adds drag & drop functionality to the tool-form component.
 */
document.addEventListener('DOMContentLoaded', function () {
    var dropContainer = document.getElementById("drop-container");

    if (! dropContainer) {
        return;
    }

    var subtitlesInput = document.getElementById("subtitles-input");

    var subInput = document.getElementById("sub-input");

    var idxInput = document.getElementById("idx-input");

    var baseFileInput = document.getElementById("base-file-input");
    var mergeFileInput = document.getElementById("merge-file-input");

    // The sub/idx tool uses different inputs, and should have
    // the default drag and drop behaviour disabled.
    var defaultDropInputExists = !! subtitlesInput;

    var hasSubIdxInputs = !! subInput && !! idxInput;

    var hasMergeInputs = !! baseFileInput && !! mergeFileInput;

    var dragLeaveTimeout = null;
    var hideDropzoneTimeout = null;

    // Check if we are on IE11. Dragging and dropping doesn't work in IE11
    if (!! window.MSInputMethodContext && !! document.documentMode) {
        return;
    }

    if (! defaultDropInputExists && ! hasSubIdxInputs && ! hasMergeInputs) {
        return;
    }


    function isDraggingFiles(dragEvent) {
        types = dragEvent.dataTransfer.types;

        return types && types.indexOf
            ? types.indexOf('Files') !== -1
            : types.contains('Files');
    }


    function showDropzoneError(text) {
        document.getElementById("dropzone-error-text").innerHTML = text;

        dropzoneError = document.getElementById("dropzone-error");

        dropzoneError.classList.remove('hidden');
        dropzoneError.classList.add('flex');

        clearTimeout(hideDropzoneTimeout);

        hideDropzoneTimeout = window.setTimeout(hideDropzoneError, 2500);
    }


    function hideDropzoneError() {
        dropzoneError = document.getElementById("dropzone-error");

        dropzoneError.classList.add('hidden');
        dropzoneError.classList.remove('flex');
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


    dropContainer.ondragenter = function (event) {
        event.preventDefault();

        if (isDraggingFiles(event)) {
            hideDropzoneError();

            dropContainer.classList.add("dropzone-active");
        }
    };


    function initDefaultBehaviour() {
        dropContainer.ondrop = function (event) {
            event.preventDefault();
            event.stopPropagation();

            dropContainer.classList.remove("dropzone-active");

            if (! isDraggingFiles(event)) {
                return;
            }

            if (! subtitlesInput.multiple && event.dataTransfer.files.length > 1) {
                subtitlesInput.value = '';

                showDropzoneError('You can only upload one file at the same time');
            } else {
                subtitlesInput.files = event.dataTransfer.files;
            }
        };
    }


    /**
     * At the sub/idx tool, you can drag either a sub or an idx file, and it will
     * be places in the correct file input.
     */
    function initSubIdxBehaviour() {
        dropContainer.ondrop = function (event) {
            event.preventDefault();
            event.stopPropagation();

            dropContainer.classList.remove("dropzone-active");

            // Unfortunately, it is not possible to upload the sub and idx file
            // at once, because you can not send each file to a different input.
            if (! isDraggingFiles(event)) {
                return;
            }

            if (event.dataTransfer.files.length > 1) {
                showDropzoneError('Drop the sub and idx file one by one');

                return;
            }

            draggedFile = event.dataTransfer.files[0];

            if (draggedFile.name.match(/\.sub$/i)) {
                subInput.files = event.dataTransfer.files;
            }

            if (draggedFile.name.match(/\.idx$/i)) {
                idxInput.files = event.dataTransfer.files;
            }
        };
    }

    /**
     * At the merge tool, the first file is put into the "base file" input,
     * and the second file into the "merge file" input.
     */
    function initMergeBehaviour() {
        dropContainer.ondrop = function (event) {
            event.preventDefault();
            event.stopPropagation();

            dropContainer.classList.remove("dropzone-active");

            if (! isDraggingFiles(event)) {
                return;
            }

            if (event.dataTransfer.files.length > 1) {
                showDropzoneError('Drop the base and merge file one by one');

                return;
            }

            draggedFile = event.dataTransfer.files[0];

            if (baseFileInput.files.length === 0) {
                baseFileInput.files = event.dataTransfer.files;
            } else if (mergeFileInput.files.length === 0) {
                mergeFileInput.files = event.dataTransfer.files;
            }
        };
    }


    defaultDropInputExists ? initDefaultBehaviour() : null;

    hasSubIdxInputs ? initSubIdxBehaviour() : null;

    hasMergeInputs ? initMergeBehaviour() : null;

}, false);
