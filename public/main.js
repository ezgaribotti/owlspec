document.addEventListener('DOMContentLoaded', function () {

    for (let index = 0; index < document.forms.length; index++) {

        // All forms use the post method

        document.forms[index].method = Object.keys(document.body.dataset)[0];
    }
});
