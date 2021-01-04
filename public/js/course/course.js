(function(root) {
	var openid = getQueryString('openid')
	var d = new Date()
	var nowMonth = d.getMonth() + 1
	var nowYear = d.getFullYear()

	// 本学期第一周的周一
	var termBegin = '2020-09-07'
	// 计算现在是第几周
	var week = getQueryString('week')
	var nowWeek = Math.ceil((Date.parse(d) - Date.parse(new Date(termBegin))) / 604800000)
	if (!week) {
		week = nowWeek
	}
	// 现在时间的学期
	if (nowMonth < 9) {
		var currentTerm = `${nowYear - 1}-${nowYear}-2`
	} else {
		var currentTerm = `${nowYear}-${nowYear + 1}-1`
	}
	// 计算当前学期
	var nowTerm = getQueryString('year')
	if (!nowTerm) {
		nowTerm = currentTerm
	}
	// 如果切换到了别的学期，隐藏日期显示
	if (nowTerm != currentTerm) {
		var weekDates = document.getElementsByClassName('days-date')
		for (var i = 0; i < weekDates.length; i ++) {
			weekDates[i].style.display = 'none'
		}
	}
	// 计算当前周跟现在周差几周
	var weekDiffNum = week - nowWeek
	var nowWeekTimestamp = Date.parse(new Date()) + weekDiffNum * 604800000
	// 计算本周日期
	var firstDayThisWeekTimestamp = Date.parse(new Date(nowWeekTimestamp)) - (((d.getDay() == 0 ? 7 : d.getDay()) - 1) * 86400000)
	for (var i = 0; i < 7; i ++) {
		var _d = new Date(firstDayThisWeekTimestamp + i * 86400000)
		document.getElementsByClassName('days-date')[i].innerHTML = `${_d.getMonth() + 1}-${_d.getDate()}`
	}

	// 计算学期候选项
	var termOption = []
	for (var i = nowYear - 5; i <= nowYear; i++) {
		termOption.push(`${i - 1}-${i}-2`)
		termOption.push(`${i}-${i + 1}-1`)
	}
	// 填充学期选项
	document.getElementById('terms').innerHTML = ''
	for (var i = 0; i < termOption.length; i ++) {
		document.getElementById('terms').innerHTML += `<option value="${termOption[i]}"`+ (termOption[i] == nowTerm ? ' selected' : '') +`>${termOption[i]}学期 <img src="image/downArrow.png" alt="" width="10px" onclick="onDownArrowClick('terms')"></option>`
	}
	// 填充周下拉选择
	document.getElementById('weeks').innerHTML = ''
	for (var i = 1; i <= 20; i ++) {
		document.getElementById('weeks').innerHTML += `<option value="${i}"`+ (i == week ? ' selected' : '') +`>第${i}周</option>`
	}
	// 刷新课表事件
	document.getElementById('menuBtn').onclick = function() {
		xalert('开始刷新课表，成功后会自动刷新页面', '提示')
		var rotateNum = 360
		document.getElementsByClassName('turnaround')[0].style.transform = `rotate(${rotateNum}deg)`
		setInterval(function() {
			rotateNum += 360
			document.getElementsByClassName('turnaround')[0].style.transform = `rotate(${rotateNum}deg)`
		}, 1000)
		setTimeout(function() {
			const openid = getQueryString('openid')
			api.course.curlGetCourse({
				openid: openid,
				year: nowTerm
			}).then(function(res) {
				if (res.code == 200) {
					location.reload()
				} else {
					alert(res.msg)
				}
			}).catch(e => {
				alert('出错了，稍后重试吧~')
			})
		}, 1000)
	}
	// 绘制课表
	for (var _cheapter = 1; _cheapter < 7; _cheapter ++) {
		for (var _day = 1; _day < 8; _day ++) {
			document.getElementById('maintable').innerHTML +=
				`<table class="maintable-cell" id="course_cell_${_day}_${_cheapter}"><tr><td>
					<div class="maintable-cell-classname" id="course_cell_name_${_day}_${_cheapter}"></div>
					<div class="maintable-cell-classroom" id="course_cell_classroom_${_day}_${_cheapter}"></div>
					<div class="maintable-cell-teachername" id="course_cell_teacher_${_day}_${_cheapter}"></div>
				</td></tr></table>`
		}
	}
	// 获取课表
	api.course.getCourse({
		openid: getQueryString('openid'),
		year: nowTerm,
		week: week
	}).then(res => {
		if (res.code == 200) {
			var colors = [
				'#F87D8A',
				'#01B3EE',
				'#13CA9A',
				'#6E9FF6',
				'#FFBA07',
				'#BA8ADF',
				'#009688',
				'#5EC0A7',
				'#9989DD',
				'#DE7DA4',
				'#99CD57'
			];
			var classNameToColor = {}
			for (var i = 0; i < res.data.length; i ++) {
				var v = res.data[i]
				if (classNameToColor[v.name]) {
					var color = classNameToColor[v.name]
				} else {
					if (colors.length == 0) {
						colors = [
							'#F87D8A',
							'#01B3EE',
							'#13CA9A',
							'#6E9FF6',
							'#FFBA07',
							'#BA8ADF',
							'#009688',
							'#5EC0A7',
							'#9989DD',
							'#DE7DA4',
							'#99CD57'
						]
					}
					var color = colors.pop()
					classNameToColor[v.name] = color
				}
				document.getElementById(`course_cell_${v.day}_${v.cheapter}`).style.backgroundColor = color
				document.getElementById(`course_cell_name_${v.day}_${v.cheapter}`).innerHTML = v.name
				document.getElementById(`course_cell_classroom_${v.day}_${v.cheapter}`).innerHTML = v.classroom
				document.getElementById(`course_cell_teacher_${v.day}_${v.cheapter}`).innerHTML = v.teacher
			}
		} else {
			alert(res.msg)
		}
	}).catch(e => {
		console.error(e)
		alert('获取课表出问题拉，稍后重试吧')
	})
	// 绑定切换学期
	document.getElementById('terms').onchange = function() {
		nowTerm = document.getElementById('terms').value
		window.location.href = `/?openid=${openid}&year=${nowTerm}&week=${week}`
	}
	// 绑定切换周
	document.getElementById('weeks').onchange = function() {
		week = document.getElementById('weeks').value
		window.location.href = `/?openid=${openid}&year=${nowTerm}&week=${week}`
	}
	// 绑定向下箭头点击
	root.onDownArrowClick = function(clickedId) {
		var e = document.createEvent("MouseEvents")
		console.log(e, clickedId)
		e.initMouseEvent("mousedown", true, true, window);
		document.getElementById(clickedId).dispatchEvent(e);
	}
	// 绑定课节时间点击
	document.getElementById(`times1`).onclick = function () {
		xalert('第1-2节<br/>综合楼 8:00~9:40 <br/>艺术、服装楼 8:00~9:30 <br/><br/>因疫情防控需要，本学期统筹安排综合楼、学院教学楼两个批次上下课时间，实施错峰上下课。<br/><br/>信息来源：教务处-新闻通知文件-通知公告', '秋季错峰上下课时间')
	}
	document.getElementById(`times2`).onclick = function () {
		xalert('第3-4节<br/>综合楼 10:05~11:45 <br/>艺术、服装楼 9:50~11:20 <br/><br/>因疫情防控需要，本学期统筹安排综合楼、学院教学楼两个批次上下课时间，实施错峰上下课。<br/><br/>信息来源：教务处-新闻通知文件-通知公告', '秋季错峰上下课时间')
	}
	document.getElementById(`times3`).onclick = function () {
		xalert('第5-6节<br/>综合楼 13:20~14:55 <br/>艺术、服装楼 13:20~14:50 <br/><br/>因疫情防控需要，本学期统筹安排综合楼、学院教学楼两个批次上下课时间，实施错峰上下课。<br/><br/>信息来源：教务处-新闻通知文件-通知公告', '秋季错峰上下课时间')
	}
	document.getElementById(`times4`).onclick = function () {
		xalert('第7-8节<br/>综合楼 15:15~16:50 <br/>艺术、服装楼 15:05~16:35 <br/><br/>因疫情防控需要，本学期统筹安排综合楼、学院教学楼两个批次上下课时间，实施错峰上下课。<br/><br/>信息来源：教务处-新闻通知文件-通知公告', '秋季错峰上下课时间')
	}
	document.getElementById(`times5`).onclick = function () {
		xalert('晚自习、选修课、重修课等课程请根据课程实际安排按时上课', '晚间上课时间')
	}
	document.getElementById(`times6`).onclick = function () {
		xalert('晚自习、选修课、重修课等课程请根据课程实际安排按时上课', '晚间上课时间')
	}
	document.getElementById('emptyCell').onclick = function() {
		window.location.href = `settings?openid=${openid}`
	}
})(this)
