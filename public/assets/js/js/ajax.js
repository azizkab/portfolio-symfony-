// public/assets/js/ajax.js

document.addEventListener('DOMContentLoaded', function () {
    const likeButtons = document.querySelectorAll('.like-btn');
    const dislikeButtons = document.querySelectorAll('.dislike-btn');

    likeButtons.forEach(button => {
        button.addEventListener('click', function () {
            handleButtonAction(this, 'like');
        });
    });

    dislikeButtons.forEach(button => {
        button.addEventListener('click', function () {
            handleButtonAction(this, 'dislike');
        });
    });

    function handleButtonAction(button, action) {
        const commentId = button.dataset.commentId;

        fetch(`/comment/${commentId}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            // Update the part of the page with the new data (e.g., the number of likes or dislikes).
            console.log(`Comment ${commentId} ${action}d!`);
            console.log(data); // The data returned by the server
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
});
