//   /// Authentication setup ///
//     

// //     var privateKey = "-----BEGIN PRIVATE KEY-----\n" +
// //     "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCrxKRoIIDNDQxZ/OeaGn5kPjQEoCX8z9i6+yVoioWyWsYKr6iSRMbSVnpPZ/vANQgdD7G3fI545PplqkyQdz9Qhw/UXgIMv+2pdSATom5C0aJoFsv/JkmAotRJYFa2NSxTGjrlXl6YOtPFZLZwD+mdn7czwrVYUapVirX2hZZ3XCFHkoqYAgLxNo8tkuHnCRCMewEN/hShKNFs/K9ewxmWsk6eDNOE0fyZ9ZUG6FC2MF5q9ZcKM7+r+x5UEMY/4OPZaDk4bg8qU2xMbBsp0hTsTlD6dsomJMKRpQkoP2Z2/7LMuplednVFhY59sG3fEwR8TfWa7CPQ8fu9BTAuYVIjAgMBAAECggEBAIpuCxXEBCm9N1Qap0ihy3TcUK3dp1/M3l0q5GYRJVitIWghUxi2hwmjGiV+pvyrgKNzL4Wxw1yZJVfj3JqdEBDFGLPNI5fE4jOsqjJIuNXu/xUmMjeqUphyIeYU1y03Q0aiIA8Uc+X5mww9c25y1JLs0gfMBrxr7HvOM4G+/Zmo+xgKeQNGGWmXeVC2rUto+nMvkJqE9oEbJ5SGUt/VIxS3dcnWHHQcwpeA3Wfdk1/eIco/1LAhbZaqmG/dPoO9HFo8796d3N9DzN7vHoePZTUzMaST3KtrsgcImP+uUIbHXmPQk3RrqMhNIlQk99it/5B9BKqAxz8asFITP6TtezECgYEA2JoyoP3gCTaTELrp9tKOdReveTm4LqLsH6nm5jZXVkzQhQgHSzgcqqHRkumUPz0vE5ibj89cxeOD5GeEFVGER6dWK5kXGJLqoDFL8UrAXjnJkgTkRg4+M41G6PZoDuxt2+uHPZVmAI5H4as3Qem1nKA7XEDyrmxcc2kzhzRDHfkCgYEAywLMiCVtjN5e59LvMF8HNR9QinhpUJTcnEgzlS3ApIGpoOeDcId8Y8Ba7eAHiL5fLIvDTcHzuWQnI9lRitMdOfnPlPwRSBAR/jl1LmPnhHh6wdbmBvzvdxMW8Fbz2vwNHX+c4S62ovcfsF+ktjCJcFbkEVvZGgnkxJrbi1eSJ/sCgYB9quDq5MCBt+cVUsyBRmIeb1KAS8ufSykhqcpsEGUVjdWBCUpqCrEfEmlsChbXpeDVRroicpWJvll8P86zK6tKgzyMaPKscDiuHkvIP61iPbbEABCM0KCn+jSE3sk6t1N5v6mkQjR+6A2uUU7q7/RNXe1ytb/XEMmGsCeULu8tyQKBgAR4TE6XHGFkqAxMra2P+hWmWdyiqBb5IB5kzJX1Su+UV0rOris1IH0FyDrCmwfcwTW24hb9NUOYu+/jIhN/cH5NTM/H+Q1wJSVG2Jir5HYbPQC1nZz7xr4FcpTaJUZmFFfY7nha0fSmolD82iCU0wHhN+ZV8mLLYvkdO8ZGqFYvAoGACXsARaCZyqanVvBKQ4fFNqvoy8fylVsE3lLnMbXBnjj69em6uUZXvwc3hImIp8v50JtMwl33L87/uJh9zAMmjA52HhPXONuoeMN6EE7eeT5sq6f1lP3EOCdshesTRidH3N4vRjALCv5KmNVqyw2VVAoSRKmszrhbf30yptWeBbs=\n"+
// //         "-----END PRIVATE KEY-----";

// /// Returns signed message ///
// qz.security.setSignaturePromise(function (toSign) {
//     return function (resolve, reject) {
//         PageMethods.SignMessage(toSign, resolve, reject);
//     };
// });


// qz.security.setSignaturePromise(function(toSign) {
//     return function(resolve, reject) {
//         try {
//             var pk = KEYUTIL.getKey(privateKey);
//             var sig = new KJUR.crypto.Signature({"alg": "SHA1withRSA"});
//             sig.init(pk); 
//             sig.updateString(toSign);
//             var hex = sig.sign();
//             console.log("DEBUG: \n\n" + stob64(hextorstr(hex)));
//             resolve(stob64(hextorstr(hex)));
//         } catch (err) {
//             console.error(err);
//             reject(err);
//         }
//     };
// });


//  METHOD 1

qz.security.setCertificatePromise(function(resolve, reject) {

            resolve("-----BEGIN CERTIFICATE-----\n"+"MIID5DCCAsygAwIBAgIJAJyA6TwbEDEnMA0GCSqGSIb3DQEBCwUAMIGFMQswCQYDVQQGEwJORzEOMAwGA1UECAwFTGFnb3MxDjAMBgNVBAcMBUxhZ29zMREwDwYDVQQKDAhXaXJlcGljazELMAkGA1UECwwCSVQxEDAOBgNVBAMMB2Jhc2h0ZW0xJDAiBgkqhkiG9w0BCQEWFXRvcGVhZGViYXNzQHlhaG9vLmNvbTAgFw0xOTAxMDIyMjQ3MzhaGA8yMDUwMDYyNzIyNDczOFowgYUxCzAJBgNVBAYTAk5HMQ4wDAYDVQQIDAVMYWdvczEOMAwGA1UEBwwFTGFnb3MxETAPBgNVBAoMCFdpcmVwaWNrMQswCQYDVQQLDAJJVDEQMA4GA1UEAwwHYmFzaHRlbTEkMCIGCSqGSIb3DQEJARYVdG9wZWFkZWJhc3NAeWFob28uY29tMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAq8SkaCCAzQ0MWfznmhp+ZD40BKAl/M/YuvslaIqFslrGCq+okkTG0lZ6T2f7wDUIHQ+xt3yOeOT6ZapMkHc/UIcP1F4CDL/tqXUgE6JuQtGiaBbL/yZJgKLUSWBWtjUsUxo65V5emDrTxWS2cA/pnZ+3M8K1WFGqVYq19oWWd1whR5KKmAIC8TaPLZLh5wkQjHsBDf4UoSjRbPyvXsMZlrJOngzThNH8mfWVBuhQtjBeavWXCjO/q/seVBDGP+Dj2Wg5OG4PKlNsTGwbKdIU7E5Q+nbKJiTCkaUJKD9mdv+yzLqZXnZ1RYWOfbBt3xMEfE31muwj0PH7vQUwLmFSIwIDAQABo1MwUTAdBgNVHQ4EFgQUkMNMUT0Pxeh+Lfnjlynn5AYqlzkwHwYDVR0jBBgwFoAUkMNMUT0Pxeh+Lfnjlynn5AYqlzkwDwYDVR0TAQH/BAUwAwEB/zANBgkqhkiG9w0BAQsFAAOCAQEAKrSM3g9k9550rv9N9Si8AR0Oh4pyrE+xmgRam4ij2dZJEqHy7popUM+uYbErKW02RakoVvae6KwQ6wWg3VNUVbipSMewfNjRheTrMLdZupu4uVlz/D+r1vn7ZZx77dvr+pCLoGzZ1pNsd0kwcl0X5xzOBCie8KSXHahp6H8fkx78UnhxzMZ0iaHef1gyhrji0LwlleG5uPm8juPTEgbCKCcq7EJCAT3I5ZTnSe+YQlb8KTVkn+27jnTatWVkcPO9pWT1EkWcs/6Jzjhgp6bFSRmUSk8d8YRS4nkU4xMDIx2MfqE/U0Gxm21cjnlgXvtJZRCmYlabxVp+lOaFQ5XBng==\n"+"-----END CERTIFICATE-----");
        });

// var privateKey ="-----BEGIN PRIVATE KEY-----\n"+"MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCrxKRoIIDNDQxZ/OeaGn5kPjQEoCX8z9i6+yVoioWyWsYKr6iSRMbSVnpPZ/vANQgdD7G3fI545PplqkyQdz9Qhw/UXgIMv+2pdSATom5C0aJoFsv/JkmAotRJYFa2NSxTGjrlXl6YOtPFZLZwD+mdn7czwrVYUapVirX2hZZ3XCFHkoqYAgLxNo8tkuHnCRCMewEN/hShKNFs/K9ewxmWsk6eDNOE0fyZ9ZUG6FC2MF5q9ZcKM7+r+x5UEMY/4OPZaDk4bg8qU2xMbBsp0hTsTlD6dsomJMKRpQkoP2Z2/7LMuplednVFhY59sG3fEwR8TfWa7CPQ8fu9BTAuYVIjAgMBAAECggEBAIpuCxXEBCm9N1Qap0ihy3TcUK3dp1/M3l0q5GYRJVitIWghUxi2hwmjGiV+pvyrgKNzL4Wxw1yZJVfj3JqdEBDFGLPNI5fE4jOsqjJIuNXu/xUmMjeqUphyIeYU1y03Q0aiIA8Uc+X5mww9c25y1JLs0gfMBrxr7HvOM4G+/Zmo+xgKeQNGGWmXeVC2rUto+nMvkJqE9oEbJ5SGUt/VIxS3dcnWHHQcwpeA3Wfdk1/eIco/1LAhbZaqmG/dPoO9HFo8796d3N9DzN7vHoePZTUzMaST3KtrsgcImP+uUIbHXmPQk3RrqMhNIlQk99it/5B9BKqAxz8asFITP6TtezECgYEA2JoyoP3gCTaTELrp9tKOdReveTm4LqLsH6nm5jZXVkzQhQgHSzgcqqHRkumUPz0vE5ibj89cxeOD5GeEFVGER6dWK5kXGJLqoDFL8UrAXjnJkgTkRg4+M41G6PZoDuxt2+uHPZVmAI5H4as3Qem1nKA7XEDyrmxcc2kzhzRDHfkCgYEAywLMiCVtjN5e59LvMF8HNR9QinhpUJTcnEgzlS3ApIGpoOeDcId8Y8Ba7eAHiL5fLIvDTcHzuWQnI9lRitMdOfnPlPwRSBAR/jl1LmPnhHh6wdbmBvzvdxMW8Fbz2vwNHX+c4S62ovcfsF+ktjCJcFbkEVvZGgnkxJrbi1eSJ/sCgYB9quDq5MCBt+cVUsyBRmIeb1KAS8ufSykhqcpsEGUVjdWBCUpqCrEfEmlsChbXpeDVRroicpWJvll8P86zK6tKgzyMaPKscDiuHkvIP61iPbbEABCM0KCn+jSE3sk6t1N5v6mkQjR+6A2uUU7q7/RNXe1ytb/XEMmGsCeULu8tyQKBgAR4TE6XHGFkqAxMra2P+hWmWdyiqBb5IB5kzJX1Su+UV0rOris1IH0FyDrCmwfcwTW24hb9NUOYu+/jIhN/cH5NTM/H+Q1wJSVG2Jir5HYbPQC1nZz7xr4FcpTaJUZmFFfY7nha0fSmolD82iCU0wHhN+ZV8mLLYvkdO8ZGqFYvAoGACXsARaCZyqanVvBKQ4fFNqvoy8fylVsE3lLnMbXBnjj69em6uUZXvwc3hImIp8v50JtMwl33L87/uJh9zAMmjA52HhPXONuoeMN6EE7eeT5sq6f1lP3EOCdshesTRidH3N4vRjALCv5KmNVqyw2VVAoSRKmszrhbf30yptWeBbs=\n"+"-----END PRIVATE KEY-----\n";

var updatedKey = "-----BEGIN RSA PRIVATE KEY-----\n"+"MIIEowIBAAKCAQEAq8SkaCCAzQ0MWfznmhp+ZD40BKAl/M/YuvslaIqFslrGCq+okkTG0lZ6T2f7wDUIHQ+xt3yOeOT6ZapMkHc/UIcP1F4CDL/tqXUgE6JuQtGiaBbL/yZJgKLUSWBWtjUsUxo65V5emDrTxWS2cA/pnZ+3M8K1WFGqVYq19oWWd1whR5KKmAIC8TaPLZLh5wkQjHsBDf4UoSjRbPyvXsMZlrJOngzThNH8mfWVBuhQtjBeavWXCjO/q/seVBDGP+Dj2Wg5OG4PKlNsTGwbKdIU7E5Q+nbKJiTCkaUJKD9mdv+yzLqZXnZ1RYWOfbBt3xMEfE31muwj0PH7vQUwLmFSIwIDAQABAoIBAQCKbgsVxAQpvTdUGqdIoct03FCt3adfzN5dKuRmESVYrSFoIVMYtocJoxolfqb8q4Cjcy+FscNcmSVX49yanRAQxRizzSOXxOIzrKoySLjV7v8VJjI3qlKYciHmFNctN0NGoiAPFHPl+ZsMPXNuctSS7NIHzAa8a+x7zjOBvv2ZqPsYCnkDRhlpl3lQtq1LaPpzL5CahPaBGyeUhlLf1SMUt3XJ1hx0HMKXgN1n3ZNf3iHKP9SwIW2Wqphv3T6DvRxaPO/endzfQ8ze7x6Hj2U1MzGkk9yra7IHCJj/rlCGx15j0JN0a6jITSJUJPfYrf+QfQSqgMc/GrBSEz+k7XsxAoGBANiaMqD94Ak2kxC66fbSjnUXr3k5uC6i7B+p5uY2V1ZM0IUIB0s4HKqh0ZLplD89LxOYm4/PXMXjg+RnhBVRhEenViuZFxiS6qAxS/FKwF45yZIE5EYOPjONRuj2aA7sbdvrhz2VZgCOR+GrN0HptZygO1xA8q5sXHNpM4c0Qx35AoGBAMsCzIglbYzeXufS7zBfBzUfUIp4aVCU3JxIM5UtwKSBqaDng3CHfGPAWu3gB4i+XyyLw03B87lkJyPZUYrTHTn5z5T8EUgQEf45dS5j54R4esHW5gb873cTFvBW89r8DR1/nOEutqL3H7BfpLYwiXBW5BFb2RoJ5MSa24tXkif7AoGAfarg6uTAgbfnFVLMgUZiHm9SgEvLn0spIanKbBBlFY3VgQlKagqxHxJpbAoW16Xg1Ua6InKVib5ZfD/OsyurSoM8jGjyrHA4rh5LyD+tYj22xAAQjNCgp/o0hN7JOrdTeb+ppEI0fugNrlFO6u/0TV3tcrW/1xDJhrAnlC7vLckCgYAEeExOlxxhZKgMTK2tj/oVplncoqgW+SAeZMyV9UrvlFdKzq4rNSB9Bcg6wpsH3ME1tuIW/TVDmLvv4yITf3B+TUzPx/kNcCUlRtiYq+R2Gz0AtZ2c+8a+BXKU2iVGZhRX2O54WtH0pqJQ/NoglNMB4TfmVfJiy2L5HTvGRqhWLwKBgAl7AEWgmcqmp1bwSkOHxTar6MvH8pVbBN5S5zG1wZ44+vXpurlGV78HN4SJiKfL+dCbTMJd9y/O/7iYfcwDJowOdh4T1zjbqHjDehBO3nk+bKun9ZT9xDgnbIXrE0YnR9zeL0YwCwr+SpjVassNlVQKEkSprM64W399MqbVngW7\n"+"-----END RSA PRIVATE KEY-----";

qz.security.setSignaturePromise(function (toSign) {
    return function (resolve, reject) {
        try {
            var pk = new RSAKey();
            pk.readPrivateKeyFromPEMString(strip(updatedKey));
            var hex = pk.signString(toSign, 'sha1');
            // console.log("DEBUG: \n\n" + stob64(hextorstr(hex)));
            resolve(stob64(hextorstr(hex)));
        } catch (err) {
            console.error(err);
            reject(err);
        }
    };
});

function strip(key) {
    if (key.indexOf('-----') !== -1) {
        return key.split('-----')[2].replace(/\r?\n|\r/g, '');
    }
}