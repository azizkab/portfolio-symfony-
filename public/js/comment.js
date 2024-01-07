// public/js/comment.js

$(document).ready(function() {
    $('.likeBtn').on('click', function(e) {
        e.preventDefault();
        var commentId = $(this).data('comment-id');
        $.ajax({
            type: 'POST',
            url: '/comment/' + commentId + '/like',
            success: function(data) {
                console.log('Like successful:', data);
                $('#likeCount' + commentId).text(data.likes);
                $('#dislikeCount' + commentId).text(data.dislikes);
            },
            error: function(xhr, status, error) {
                console.error('Like error:', error);
            }
        });
    });

    $('.dislikeBtn').on('click', function(e) {
        e.preventDefault();
        var commentId = $(this).data('comment-id');
        $.ajax({
            type: 'POST',
            url: '/comment/' + commentId + '/dislike',
            success: function(data) {
                console.log('Dislike successful:', data);
                $('#likeCount' + commentId).text(data.likes);
                $('#dislikeCount' + commentId).text(data.dislikes);
            },
            error: function(xhr, status, error) {
                console.error('Dislike error:', error);
            }
        });
    });
});
