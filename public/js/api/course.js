(function(root) {
    if (!root.api) {
        root.api = {}
    }
    root.api.course = {
        loginJiaowu: function(data) {
            return post('course/loginJiaowu', data)
        },
        curlGetCourse: function(data) {
            return get('course/curlGetCourse', data)
        },
        getCourse: function(data) {
            return get('course/getCourse', data)
        },
        sendFeedback: function (data) {
            return post('course/feedback', data)
        },
        getSmsAmount: function (data) {
            return post('course/getSmsAmount', data)
        },
        unsubscribeSms: function(data) {
            return post('course/unsubscribeSms', data)
        },
        subscribeSendVerifycode: function(data) {
            return post('course/subscribeSendVerifycode', data)
        },
        subscribeSms: function(data) {
            return post('course/subscribeSms', data)
        },
        setNomoreVipAd: function(data) {
            return post('course/setNomoreVipAd', data)
        }
    }
})(this)