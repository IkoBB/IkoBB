$(document).ready(function () {
    //Setting up the Wysibb Editor
    $('#editor').wysibb({});

    //The syntex highlighting in <pre> tags
    $('pre').each(function (i, e) {
        hljs.highlightBlock(e)
    });

    //The tags used in conversations.
    $('#receiver').tagsInput({
        defaultText: 'Add a user',
        'width': '100%',
        'height': 'auto'
    });
});

var formsave1 = new autosaveform({
    formid: 'tango_form',
    pause: 1000 //<--no comma following last option!
});

/*
 * Forum functions
 */

//Quote Post
function quote(id) {
    $('#editor').execCommand('quote', {author: '', seltext: 'Post ID: ' + id + ''});
}

//Inserting Smilies
function add_emoji(text) {
    console.log(text);
    var ori_val = $('#editor').val();
    $('#editor').val(ori_val + text);
}
