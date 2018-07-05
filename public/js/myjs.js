$(function () {

    // used in path: resources\views\threads\show.blade.php
    $('.likeArea').on('click', '.likeReplyBtnToggle', function () {

        $likeArea = $(this).parent();
        $id = $likeArea.find('.replyId').val();

        axios.post('/users/likereply', {
            reply_id: $id,
        }).then((response) => {
            $likeArea.find('.likesCounter').text(response.data.likesCount);
            $(this).text(response.data.was_it_like_or_unlike ? 'Unlike' : 'Like');
        }).catch((error) => {
            console.log(error);
        });
    });

    // used in path: resources\views\threads\index.blade.php
    $('.deleteThreadArea').on('click', '.deleteThreadBtn', function () {

        $deleteThreadArea = $(this).parent();
        $threadBox = $deleteThreadArea.closest('.thread');
        $id = $deleteThreadArea.find('.threadId').val();

        axios.post('/threads/delete', {
            _method: 'delete',
            thread_id: $id,
        }).then((response) => {
            if (response.data.state === true) {
                $($threadBox).fadeOut(1000);
            } else {
                console.log('thread cannot be deleted due to server issue.');
            }
        }).catch((error) => {
            console.log(error);
        });
    });

    // used in path: resources\views\layouts\app.blade.php
    $('.redirect-alert').fadeOut(5000);

    // used in path: resources\views\threads\show.blade.php
    var link = window.location.href;
    // match '#reply-' followed by digit.
    var intendedReply = link.match(/(?<name>#reply-)\d+/)[0];
    // get the digit from matched hash above, to be used as the id
    var replyId = intendedReply.match(/\d+/)[0];
    $reply = $('#reply-body-' + replyId);
    // do the background-color animated change.
    $reply.css({"background-color": "#f4a83d", "transition": "background-color 1.8s ease"});
    setTimeout(function () {
        $reply.css("background-color", "#ffffff");
    }, 2000);

});