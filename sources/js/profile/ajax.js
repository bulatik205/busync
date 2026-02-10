const successProfieBlock = document.getElementById("profile-success");
const errorProfieBlock = document.getElementById("profile-error");

async function saveProfile(API_KEY) {
    const firstName = document.getElementById("first_name").value ?? "-";
    const lastName = document.getElementById("last_name").value ?? "-";
    const fatherName = document.getElementById("father_name").value ?? "-";
    const selected = document.querySelector('input[name="business_type"]:checked');
    const businessType = selected?.value ?? "-";
    const businessSite = document.getElementById("business_site").value ?? "-";
    const phone = document.getElementById("phone").value ?? "-";

    try {
        const response = await fetch(`http://localhost/busync/api/v1/saveProfile/`, {
            method: 'POST',
            headers: {
                'API-key': API_KEY,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                "fields": {
                    "first_name": firstName, 
                    "last_name": lastName, 
                    "father_name": fatherName, 
                    "business_type": businessType, 
                    "business_site": businessSite, 
                    "phone": phone
                }
            })
        });

        const result = await response.json(); 

        if (result.success) {
            successProfieBlock.style.display = "block";

            setTimeout(() => {
                successProfieBlock.style.display = "none";
            }, 2000);
        } else {
            errorProfieBlock.style.display = "block";

            setTimeout(() => {
                errorProfieBlock.style.display = "none";
            }, 2000);
            console.log(result.error.message)
        }
    } catch (error) {
        console.log(error);
    }
}