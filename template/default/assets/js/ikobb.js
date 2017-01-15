/**
 *
 * This file is part of IkoBB Forum and belongs to the module <CMS>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
/**
 * Created by Marcel on 14.01.2017.
 */
var iko = {
    ajax: "./ajax.php",
    SSID: IkoBB_SSID,
    login: function () {
        var username = document.forms["login"]["username"].value;
        var password = document.forms["login"]["password"].value;
        $.post(this.ajax, {
            ikobb_ssid: this.SSID, module: "user", func: "login", user: username,
            pass: password
        }, function (data) {
            if (data == "TRUE") {
                $(".login-box .info-box-text").text("Login success");
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            }
            else {
                $(".login-box .info-box-text").text("Login failed");
                console.info(data);
            }
        });
    },
    registration: function () {
        var username = document.forms["registration"]["username"].value;
        var email = document.forms["registration"]["email"].value;
        var password = document.forms["registration"]["password"].value;
        $.post(this.ajax, {
            ikobb_ssid: this.SSID,
            module: "user",
            func: "regist",
            user: username,
            pass: password,
            email: email
        }, function (data) {
            if (data == "TRUE") {
                setTimeout(function () {
                    window.location = "./index.php?module=user&page=regist&data=true";
                }, 2000);
            }
            else {

            }
        });
    }
};