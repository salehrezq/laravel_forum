$(function () {

    $('.likeArea').on('click', '.likeReplyBtnToggle', function (e) {

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
});