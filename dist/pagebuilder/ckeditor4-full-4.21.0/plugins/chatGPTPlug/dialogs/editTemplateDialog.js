CKEDITOR.dialog.add('editTemplateDialog', function(editor) {
    function getSelectedTemplate() {
        const index = editor.config.chatgptSettings.selectedTemplate;
        return index !== null ? editor.config.chatgptSettings.templates[index] : { name: '', inputs: [] };
    }

    return {
        title: 'Edit Template',
        minWidth: 400,
        minHeight: 300,
        contents: [
            {
                id: 'editTemplate',
                label: 'Edit Template',
                elements: [
                    {
                        type: 'text',
                        id: 'templateName',
                        label: 'Template Name',
                        validate: CKEDITOR.dialog.validate.notEmpty("Template name cannot be empty."),
                        setup: function() {
                            const template = getSelectedTemplate();
                            this.setValue(template.name);
                        }
                    },
                    {
                        type: 'fieldset',
                        id: 'templateInputs',
                        label: 'Template Inputs',
                        children: Array.from({ length: 5 }, (_, i) => ({
                            type: 'text',
                            id: `input${i + 1}`,
                            label: `Input ${i + 1}`,
                            setup: function() {
                                const template = getSelectedTemplate();
                                this.setValue(template.inputs[i] || '');
                            }
                        }))
                    }
                ]
            }
        ],
        onOk: function() {
            const dialog = this;
            const template = getSelectedTemplate();
            template.name = dialog.getValueOf('editTemplate', 'templateName');
            for (let i = 0; i < 5; i++) {
                template.inputs[i] = dialog.getValueOf('editTemplate', `input${i + 1}`) || '';
            }

            if (editor.config.chatgptSettings.selectedTemplate === null) {
                editor.config.chatgptSettings.templates.push(template);
            } else {
                editor.config.chatgptSettings.templates[editor.config.chatgptSettings.selectedTemplate] = template;
                editor.config.chatgptSettings.selectedTemplate = null;
            }

            localStorage.setItem('chatgptTemplates', JSON.stringify(editor.config.chatgptSettings.templates));
        },
        onShow: function() {
            const dialog = this;
            const template = getSelectedTemplate();
            dialog.setValueOf('editTemplate', 'templateName', template.name);
            for (let i = 0; i < 5; i++) {
                dialog.setValueOf('editTemplate', `input${i + 1}`, template.inputs[i] || '');
            }
        },
        buttons: [CKEDITOR.dialog.okButton, CKEDITOR.dialog.cancelButton]
    };
});