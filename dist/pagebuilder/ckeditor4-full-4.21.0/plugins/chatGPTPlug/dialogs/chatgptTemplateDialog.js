CKEDITOR.dialog.add('chatgptTemplateDialog', function(editor) {
    function updateTemplateList() {
        const templateListDiv = document.getElementById('template-list');
        const templates = editor.config.chatgptSettings.templates;
        templateListDiv.innerHTML = templates.map((template, index) =>
            `<div>${index + 1}. ${template.name} <button type="button" onclick="CKEDITOR.dialog.getCurrent().editTemplate(${index})">Edit</button> <button type="button" onclick="CKEDITOR.dialog.getCurrent().deleteTemplate(${index})">Delete</button></div>`
        ).join('');
    }

    return {
        title: 'Manage Templates',
        minWidth: 400,
        minHeight: 300,
        contents: [
            {
                id: 'templates',
                label: 'Templates',
                elements: [
                    {
                        type: 'html',
                        id: 'templateList',
                        html: '<div id="template-list"></div>'
                    },
                    {
                        type: 'button',
                        id: 'addTemplate',
                        label: 'Add Template',
                        onClick: function() {
                            CKEDITOR.dialog.getCurrent().hide();
                            editor.config.chatgptSettings.selectedTemplate = null;
                            editor.openDialog('editTemplateDialog');
                        }
                    }
                ]
            }
        ],
        onShow: function() {
            updateTemplateList();
        },
        onOk: function() {
            editor.config.chatgptSettings.selectedTemplate = null;
        },
        buttons: [CKEDITOR.dialog.okButton, CKEDITOR.dialog.cancelButton],
        editTemplate: function(index) {
            editor.config.chatgptSettings.selectedTemplate = index;
            this.hide();
            editor.openDialog('editTemplateDialog');
        },
        deleteTemplate: function(index) {
            editor.config.chatgptSettings.templates.splice(index, 1);
            localStorage.setItem('chatgptTemplates', JSON.stringify(editor.config.chatgptSettings.templates));
            updateTemplateList();
        }
    };
});