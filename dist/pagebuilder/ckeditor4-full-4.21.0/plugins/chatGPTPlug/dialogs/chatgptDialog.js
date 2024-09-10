CKEDITOR.dialog.add('chatgptDialog', function(editor) {
    return {
        title: 'Generate AI Text',
        minWidth: 400,
        minHeight: 200,
        contents: [
            {
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [
                    {
                        type: 'textarea',
                        id: 'prompt',
                        label: 'Enter your prompt',
                        validate: CKEDITOR.dialog.validate.notEmpty("Prompt field cannot be empty.")
                    }
                ]
            }
        ],
        onOk: function() {
            const dialog = this;
            const prompt = dialog.getValueOf('tab-basic', 'prompt');
            
            generateText(prompt, editor.config.chatgptMaxTokens, function(generatedText) {
                editor.setData(generatedText);
                selectInsertedText(editor, generatedText);
            });
        }
    };
});