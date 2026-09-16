function addMagnetName() {
    let magnetLink = document.getElementById("linkForm_link").value;
    let magnetName = document.getElementById("linkForm_name").value;

    if (magnetLink && !magnetName) {
        let dnParam = magnetLink.split('&').find(part => part.startsWith("dn="));
        
        if (dnParam) {
            let extractedName = dnParam.substring(3).replace(/\+/g, ' ');
            
            try {
                let decoded = decodeURIComponent(extractedName).trim();
                document.getElementById("linkForm_name").value = decoded || "UNDEFINED";
            } catch (e) {
                // Ak zlyhá decodeURIComponent, použijeme neplatný reťazec alebo UNDEFINED
                document.getElementById("linkForm_name").value = extractedName.trim() || "UNDEFINED";
            }
        } else {
            // Ak sa parameter "dn=" v odkaze vôbec nenachádza
            document.getElementById("linkForm_name").value = "UNDEFINED";
        }
    }
}
