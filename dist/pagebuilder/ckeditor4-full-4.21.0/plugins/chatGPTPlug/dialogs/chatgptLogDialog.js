CKEDITOR.dialog.add('chatgptLogDialog', function(editor) {
    return {
        title: 'AI Text Generation Log',
        minWidth: 600,
        minHeight: 400,
        contents: [
            {
                id: 'log',
                label: 'Log',
                elements: [
                    {
                        type: 'html',
                        id: 'logContent',
                        html: '<div id="chatgpt-log" style="height: 300px; overflow-y: scroll;"></div>'
                    }
                ]
            }
        ],
        onShow: function() {
            const logContentDiv = document.getElementById('chatgpt-log');
            const logs = JSON.parse(localStorage.getItem('chatgptLog')) || [];
            logContentDiv.innerHTML = logs.map(log => `<p>[${log.timestamp}] ${log.message}</p>`).join('');
        }
    };
});