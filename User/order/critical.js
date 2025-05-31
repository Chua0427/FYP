// showUpdateFeedback provides visual feedback on cart updates
function showUpdateFeedback(element, status) {
    var className = status === 'success' ? 'update-success' : 'update-error';
    element.classList.add(className);
    setTimeout(function() {
        element.classList.remove('update-success');
        element.classList.remove('update-error');
    }, 1000);
} 