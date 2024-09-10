CKEDITOR.dialog.add('chatgptSettingsDialog', function(editor) {
    return {
        title: 'AI Text Settings',
        minWidth: 300,
        minHeight: 150,
        contents: [
            {
                id: 'settings',
                label: 'Settings',
                elements: [
                    {
                        type: 'checkbox',
                        id: 'generateDirectly',
                        label: 'Generate Directly',
                        'default': editor.config.chatgptSettings.generateDirectly,
                        onClick: function() {
                            editor.config.chatgptSettings.generateDirectly = this.getValue();
                        }
                    },
                    {
                        type: 'text',
                        id: 'maxTokens',
                        label: 'Max Tokens',
                        'default': editor.config.chatgptSettings.maxTokens,
                        validate: CKEDITOR.dialog.validate.integer('Max Tokens must be an integer.'),
                        onChange: function() {
                            editor.config.chatgptSettings.maxTokens = parseInt(this.getValue(), 10);
                        }
                    },
                    {
                        type: 'text',
                        id: 'apiKey',
                        label: 'API Key',
                        'default': editor.config.chatgptSettings.apiKey,
                        onChange: function() {
                            editor.config.chatgptSettings.apiKey = this.getValue();
                        }
                    }
                ]
            }
        ],
        onOk: function() {
            const dialog = this;
            editor.config.chatgptSettings.generateDirectly = dialog.getValueOf('settings', 'generateDirectly');
            editor.config.chatgptSettings.maxTokens = parseInt(dialog.getValueOf('settings', 'maxTokens'), 10);
            editor.config.chatgptSettings.apiKey = dialog.getValueOf('settings', 'apiKey');
            localStorage.setItem('chatgptSettings', JSON.stringify(editor.config.chatgptSettings));
        }
    };
});