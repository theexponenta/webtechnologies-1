
function trimPath(path) {
    let startIndex = 0;
    let endIndex = path.length - 1;

    if (path.startsWith("/"))
        startIndex = 1;

    if (path.endsWith("/"))
        endIndex = path.length - 1;

    return path.substring(startIndex, endIndex + 1);
}


function base64ToUtf8(encoded) {
    let binary = atob(encoded);
    let bytes = new Uint8Array(binary.length);
    for (let i = 0; i < bytes.length; i++) {
      bytes[i] = binary.charCodeAt(i);
    }
    return String.fromCharCode(...new Uint16Array(bytes.buffer));
}


function createNewDirectory(path, newDirectoryName) {
    return fetch("/admin?action=createDirectory", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ path: trimPath(path) + "/" + newDirectoryName })
    }).then(response => response.json()) 
}


function createNewFile(path, newFileName) {
    return fetch("/admin?action=createFile", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ path: trimPath(path) + "/" + newFileName })
    }).then(response => response.json());
}


function saveFile(path, contents, isBase64) {
    return fetch("/admin?action=saveFile", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ path: trimPath(path), contents: contents, isBase64: isBase64 })
    }).then(response => response.json());
}


function deleteFile(path) {
    return fetch("/admin?action=deleteFile", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ path: trimPath(path) })
    }).then(response => response.json());
}


function getFileContents(path) {
    return fetch("/admin?action=getFileContents&path=" + encodeURIComponent(trimPath(path)));
}


function getCurrentPath() {
    let searchParams = new URLSearchParams(window.location.search);
    let path = searchParams.get("path");
    if (!path)
        path = ""; 

    return trimPath(path);
}   


function showFileDialog(filename) {
    let path = getCurrentPath() + "/" + filename;
    window.openedFilePath = path;

    getFileContents(path).then(response => {
        if (response.ok) {
            response.json().then(json => {
                let fileContents = document.getElementById("file-contents");
                fileContents.value = json["contents"];

                let fileDialog = document.getElementById("file-content-dialog");
                fileDialog.style.display = "flex";
            });
        } else {
            alert("Failed to get file contents");
        }
    });
}


function closeFileDialog() {
    let fileDialog = document.getElementById("file-content-dialog");
    fileDialog.style.display = "none";
}


function saveFileContents() {
    let fileContents = document.getElementById("file-contents").value;
    saveFile(window.openedFilePath, fileContents, false);
    closeFileDialog();
}


function fileListItemClickHandler(e) {
    let fileType = e.currentTarget.dataset.fileType;
    if (fileType === "directory") {
        let path = getCurrentPath();

        let filename = e.currentTarget.dataset.fileName;
        if (filename === "..") {
            let pathParts = path.split("/");
            pathParts.pop();
            path = pathParts.join("/");
        } else {
            path += "/" + filename;
        }
        
        let searchParams = new URLSearchParams(window.location.search);
        searchParams.set("path", path);
        window.location.search = searchParams.toString();
    } else if (fileType === "regular") {
        showFileDialog(e.currentTarget.dataset.fileName);
    }
}


function newFileBtnClickHandler(e) {
    let newFileName = prompt("Enter new file name:");
    if (newFileName) {
        let path = getCurrentPath();

        createNewFile(path, newFileName).then(response => {
            if (response.success) {
                window.location.reload();
            } else {
                alert("Failed to create new file: " + response.error);
            }
        });
    }
}


function newFolderBtnClickHandler(e) {
    let newFolderName = prompt("Enter new folder name:");
    if (newFolderName) {
        let path = getCurrentPath();

        createNewDirectory(path, newFolderName).then(response => {
            if (response.success) {
                window.location.reload();
            } else {
                alert("Failed to create new folder: " + response.error);
            }
        });
    }
}


function uploadBtnClickHandler(e) {
    let uploadFileInput = document.getElementById("upload-file-input");
    uploadFileInput.click();
}


function fileChangeHandler(e) {
    let filesList = e.target.files;
    if (filesList.length == 0)
        return;

    let file = filesList[0];
    e.target.value = null;

    let reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function() {
        let result = reader.result;
        let commaIndex = result.indexOf(",");
        let data = result.substring(commaIndex + 1);

        let path = getCurrentPath() + "/" + file.name;
        saveFile(path, data, true).then(response => {
            if (response.success) {
                window.location.reload();
            } else {
                alert("Failed to save file: " + response.error);
            }
        });
    };
}


function selectFileListItem(e) {
    let selectedItems = document.querySelectorAll(".c-hEpWEg-selected");
    for (let item of selectedItems) {
        item.classList.remove("c-hEpWEg-selected");
    }

    e.currentTarget.classList.add("c-hEpWEg-selected");
}


function deleteButtonClickHandler(e) {
    let selectedItem = document.querySelector(".c-hEpWEg-selected");
    if (!selectedItem) {
        alert("No file selected");
        return;
    }

    let answer = confirm("Are you sure you want to delete this file?");
    if (!answer)
        return;

    let fileName = selectedItem.dataset.fileName;
    deleteFile(getCurrentPath() + "/" + fileName).then(response => {
        if (response.success) {
            window.location.reload();
        } else {
            alert("Failed to delete file: " + response.error);
        }
    });
}


document.addEventListener("DOMContentLoaded", function() {
    let fileListItems = document.querySelectorAll(".c-hEpWEg");
    for (let item of fileListItems) {
        item.addEventListener("dblclick", fileListItemClickHandler);
        item.addEventListener("click", selectFileListItem)
    }

    let newFolderBtn = document.getElementById("new-folder-btn");
    newFolderBtn.addEventListener("click", newFolderBtnClickHandler);

    let newFileBtn = document.getElementById("new-file-btn");
    newFileBtn.addEventListener("click", newFileBtnClickHandler);

    let uploadBtn = document.getElementById("upload-btn");
    uploadBtn.addEventListener("click", uploadBtnClickHandler);

    let changeFileInput = document.getElementById("upload-file-input");
    changeFileInput.addEventListener("change", fileChangeHandler);

    let deleteBtn = document.getElementById("delete-btn");
    deleteBtn.addEventListener("click", deleteButtonClickHandler);
    
    let exitBtn = document.getElementById("exit-btn");
    exitBtn.addEventListener("click", closeFileDialog);

    let saveFileBtn = document.getElementById("save-file-btn");
    saveFileBtn.addEventListener("click", saveFileContents);
});

