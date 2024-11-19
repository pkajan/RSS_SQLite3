function addMagnetName() {
	let magnetLink = document.getElementById("linkForm_link").value;
	let magnetName = document.getElementById("linkForm_name").value;
	if (magnetLink && !magnetName) {
		document.getElementById("linkForm_name").value = decodeURIComponent(magnetLink.split('&').find(part => part.startsWith("dn="))?.substring(3).replace(/\+/g, ' '));
	}
}
