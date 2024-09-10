// This plugin is developed and maintained by ProPixel Bv. 
// Unauthorized copying, modification, distribution, or use of this code is strictly prohibited.
// This is proprietary software, and all rights are reserved by ProPixel Bv.
// Any misuse or unauthorized distribution of this code may result in legal action.
// © ProPixel Bv. All rights reserved.

class ChatGPTPlug {
    constructor(editor) {
        this.editor = editor;
        this.pluginPath = CKEDITOR.plugins.getPath('chatGPTPlug');

        // Initialize settings without exposing the API key
        CKEDITOR.config.chatgptSettings = CKEDITOR.config.chatgptSettings || this.getDefaultSettings();
        this.settings = CKEDITOR.config.chatgptSettings;

        this.wordCount = 60;  // Default word count if no custom input is provided

        this.init();
    }

    getDefaultSettings() {
        return {
            headerPrompt: 'Schrijf een pakkende en duidelijke koptekst voor het volgende onderwerp:',
            paragraphPrompt: 'Schrijf een gedetailleerde paragraaf van maximaal 60 woorden voor het volgende onderwerp:',
        };
    }

    init() {
        this.log('Initializing ChatGPTPlug...');
        this.addCommands();
        this.addToolbarButtons(); // Add two buttons: one for text generation and one for word count
    }

    addCommands() {
        // Command to generate text based on selected block (header or paragraph) and word count
        this.editor.addCommand('generateDynamicText', {
            exec: (editor) => {
                console.log('Executing generateDynamicText command...');

                const selectedText = this.getSelectedBlockText(editor);
                const selectedTag = this.getSelectedTag(editor);
                if (selectedText && selectedTag) {
                    // Send both the selected text, tag, and word count to the backend
                    const payload = {
                        text: selectedText,
                        tag: selectedTag,
                        wordCount: this.wordCount  // Include word count in payload
                    };
                    console.log('Sending payload:', payload);

                    this.makeBackendRequest(payload, (generatedText) => {
                        console.log('Generated text:', generatedText);
                        this.replaceSelectedText(editor, generatedText);
                    });
                } else {
                    this.showErrorNotification('Please select a block of text.');
                }
            }
        });

        // Command to set the word count from an input field
        this.editor.addCommand('setWordCount', {
            exec: (editor) => {
                const customWordCount = prompt('Specificeer hoeveel woorden. Bij headings is het aantal characters:', '60');
                if (customWordCount && !isNaN(customWordCount)) {
                    this.wordCount = parseInt(customWordCount, 10);
                    console.log('Word count set to:', this.wordCount);
                } else {
                    this.wordCount = 60;  // Reset to default if no valid input
                    console.log('Word count reset to default (60 words)');
                }
            }
        });
    }

    // Function to make the backend request to the PHP server
    makeBackendRequest(payload, callback) {
        fetch('https://dev01.propixel.nl/chatGPTPlugin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)  // Send the selected text, tag, and word count to the backend
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.generatedText) {
                callback(data.generatedText);
            } else {
                this.showErrorNotification('Error generating text from backend.');
            }
        })
        .catch(error => {
            console.error('Backend request failed:', error);
            this.showErrorNotification('Failed to connect to backend.');
        });
    }

    // Function to get the entire block (e.g., <p>, <h1>) containing the selected text
    getSelectedBlockText(editor) {
        const selection = editor.getSelection();
        const selectedElement = selection.getStartElement();
        if (selectedElement) {
            const blockElement = selectedElement.getAscendant('p', true) || selectedElement.getAscendant('h1', true) ||
                                 selectedElement.getAscendant('h2', true) || selectedElement.getAscendant('h3', true) ||
                                 selectedElement.getAscendant('h4', true) || selectedElement.getAscendant('h5', true) ||
                                 selectedElement.getAscendant('h6', true) || selectedElement.getAscendant('div', true);

            if (blockElement) {
                const blockText = blockElement.getText();  // Get the text inside the block element
                console.log('Selected block text:', blockText);
                return blockText.trim() !== '' ? blockText : null;
            }
        }
        return null; // Fallback if no block element is found
    }

    // Function to get the selected block's tag name (e.g., h1, p)
    getSelectedTag(editor) {
        const selection = editor.getSelection();
        const selectedElement = selection ? selection.getStartElement() : null;
        const tagName = selectedElement ? selectedElement.getName() : null;
        console.log('Selected tag:', tagName);
        return tagName;
    }

    // Function to replace the manually selected text
    replaceSelectedText(editor, generatedText) {
        const selection = editor.getSelection();
        const range = selection.getRanges()[0]; // Get the current selection range

        if (range) {
            editor.insertHtml(generatedText); // Insert the generated text into the selected range
            console.log('Replaced selected text with:', generatedText);
        } else {
            console.error('No text selected to replace.');
            this.showErrorNotification('No text selected to replace.');
        }
    }

    addToolbarButtons() {
        // Add the text generation button
        this.editor.ui.addButton('chatGPTGenerateTextButton', {
            label: 'Generate Text',
            command: 'generateDynamicText', // Command for dynamic text generation
            toolbar: 'insert',
            icon: this.pluginPath + 'icons/chatgpt_logo.svg' // Use a general icon for text generation
        });

        // Add the word count button
        this.editor.ui.addButton('chatGPTWordCountButton', {
            label: 'Set Word Count',
            command: 'setWordCount',  // Command to set custom word count
            toolbar: 'insert',
            icon: this.pluginPath + 'icons/chatgpt_logo.svg' // Icon for word count button
        });
    }

    showErrorNotification(message) {
        const editorInstance = CKEDITOR.instances[Object.keys(CKEDITOR.instances)[0]];
        if (editorInstance) {
            editorInstance.showNotification(message, 'warning', 5000);
        } else {
            alert(message);
        }
    }

    log(message) {
        const logs = JSON.parse(localStorage.getItem('chatgptLog')) || [];
        logs.push({ timestamp: new Date().toISOString(), message });
        localStorage.setItem('chatgptLog', JSON.stringify(logs));
    }
}

CKEDITOR.plugins.add('chatGPTPlug', {
    icons: 'chatgpt_text,chatgpt_wordcount',
    init: function (editor) {
        new ChatGPTPlug(editor);
    }
});