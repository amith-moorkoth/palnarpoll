/**
 * Created by amith on 2/19/2016.
 */

function onSignIn(googleUser) {
    // Useful data for your client-side scripts:
    var profile = googleUser.getBasicProfile();
    //console.log("ID: " + profile.getId()); // Don't send this directly to your server!
    //console.log("Name: " + profile.getName());
    //console.log("Email: " + profile.getEmail());

    // The ID token you need to pass to your backend:
    //var id_token = googleUser.getAuthResponse().id_token;
    //console.log("ID Token: " + id_token);

    $.ajax({
        type: 'post',
        url: '/authenticate',
        data: {
            'name':  profile.getName(),
            'email_id': profile.getEmail()
        },
        success: function (response) {
            window.location.assign("/redirects");
        },
        error: function () {
            alert("error");
            signOut();
        }
    });
};

function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
        window.location.assign("/");
    });
};
function onSignInFailure(googleUser){

}
