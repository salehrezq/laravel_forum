$(function () {

    $('.likeArea').on('click', '.likeReplyBtnToggle', function () {

        $likeArea = $(this).parent();
        $id = $likeArea.find('.replyId').val();

        axios.post('/users/likereply', {
            reply_id: $id,
        }).then((response) => {
            $likeArea.find('.likesCounter').text(response.data.likescount);
            $(this).text(response.data.was_it_like_or_unlick ? 'Unlike' : 'Like');
        }).catch((error) => {
            console.log(error);
        });
    });

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
});