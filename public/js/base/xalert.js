{/* <div id="cover">
    <div class="cover-alert" id="cover-alert">
    <div class="cover-alert-title" id="xalert-title">

    </div>
    <div class="cover-alert-content" id="xalert-content">

    </div>
    <div class="cover-alert-btn" id="xalert-btn">好的</div>
    </div>
    </div> */}

// #cover {
//     position: fixed;
//     top: 0;
//     left: 0;
//     width: 100vw;
//     height: 100vh;
//     background-color: rgba(0, 0, 0, 0.5);
//     /*display: flex;*/
//     display: none;
//     flex-direction: column;
//     justify-content: center;
//     align-items: center;
//     z-index: 10000;
//     transition: all linear 0.5s;
// }
// .cover-alert {
//     position: relative;
//     width: 80vw;
//     min-height: 20vh;
//     border-radius: 20px;
//     background-color: white;
//     transition: all linear 0.2s;
//     display: none;
// }
// .cover-alert-title {
//     width: 100%;
//     text-align: center;
//     margin-top: 20px;
//     font-size: 18px;
// }
// .cover-alert-content {
//     margin: 10px 0px;
//     margin-bottom: 60px;
//     padding: 0 20px;
// }
// .cover-alert-btn {
//     position: absolute;
//     bottom: 0;
//     width: 100%;
//     height: 44px;
//     line-height: 44px;
//     text-align: center;
//     /*margin-top: 30px;*/
//     margin-bottom: 10px;
// }
// .cover-alert-btn:active {
//     background-color: #efeff5;
// }
(function(root) {
    root.xalert = function (msg, title = false, cb = false) {
        if (title) {
            document.getElementById('xalert-title').style.display = ''
            document.getElementById('xalert-title').innerHTML = title
        } else {
            document.getElementById('xalert-title').style.display = 'none'
        }
        document.getElementById('xalert-content').innerHTML = msg
        document.getElementById('cover').style.display = 'flex'
        document.getElementById('cover').style.opacity = 1
        document.getElementById('cover-alert').style.display = 'inline-block'
        document.getElementById('cover-alert').style.width = '80vw'
        document.getElementById('cover-alert').style.minHeight = '20vh'

        document.getElementById('xalert-btn').onclick = function() {
            document.getElementById('xalert-title').innerHTML = ''
            document.getElementById('xalert-title').style.display = 'none'
            document.getElementById('xalert-content').innerHTML = ''
    
            setTimeout(function () {
                document.getElementById('cover-alert').style.display = 'none'
                document.getElementById('cover').style.display = 'none'
    
                cb && cb()
            }, 500)
            document.getElementById('cover').style.opacity = 0
            document.getElementById('cover-alert').style.width = '0'
            document.getElementById('cover-alert').style.minHeight = '0'
        }
    }
})(this)
