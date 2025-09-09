// DOM elements
const video = document.getElementById('cameraView');
const canvas = document.getElementById('photoCanvas');
const captureBtn = document.getElementById('captureBtn');
const retakeBtn = document.getElementById('retakeBtn');
const sendBtn = document.getElementById('sendBtn');
const photoPreview = document.getElementById('photoPreview');
const previewImage = document.getElementById('previewImage');
const resultDiv = document.getElementById('result');
const loadingDiv = document.getElementById('loading');

let stream = null;

// Start de camera
async function startCamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        });
        video.srcObject = stream;
    } catch (err) {
        console.error('Fout bij toegang camera:', err);
        showError('Camera Fout', 'Kon geen toegang krijgen tot de camera. Controleer uw toestemmingen en zorg dat u HTTPS gebruikt.');
    }
}

// Maak een foto
captureBtn.addEventListener('click', function() {
    const context = canvas.getContext('2d');

    // Stel canvas grootte in op video grootte
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    // Teken het huidige video frame op het canvas
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Toon de preview
    previewImage.src = canvas.toDataURL('image/jpeg');
    photoPreview.classList.remove('hidden');
    captureBtn.classList.add('hidden');

    // Stop de camera
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
});

// Opnieuw proberen
retakeBtn.addEventListener('click', function() {
    photoPreview.classList.add('hidden');
    resultDiv.classList.add('hidden');
    captureBtn.classList.remove('hidden');
    clearResultDiv();
    startCamera();
});

// Verstuur foto direct naar Plant.id API
sendBtn.addEventListener('click', async function() {
    loadingDiv.classList.remove('hidden');
    photoPreview.classList.add('hidden');
    sendBtn.disabled = true;

    try {
        // Converteer canvas naar blob
        canvas.toBlob(async function(blob) {
            try {
                // Controleer of API key is ingesteld
                if (!PLANT_ID_API_KEY || PLANT_ID_API_KEY === 'Tzjm3d6QtmenotzI7SZjpPyrZUXm41gZF1xuc1ixBKEc6qk1gK') {
                    throw new Error('API key niet ingesteld. Vul je Plant.id API key in config.js in.');
                }

                // Converteer blob naar base64
                const base64Image = await blobToBase64(blob);

                // Verwijder data:image/jpeg;base64, prefix
                const base64Data = base64Image.split(',')[1];


                const requestData = {
                    images: [base64Data],
                    similar_images: true,
                    health: 'all'
                };

                console.log('Sending request to Plant.id API...', requestData);

                // Verstuur direct naar Plant.id API
                const response = await fetch(PLANT_ID_API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Api-Key': PLANT_ID_API_KEY
                    },
                    body: JSON.stringify(requestData)
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`API error: ${response.status} ${response.statusText} - ${errorText}`);
                }

                const data = await response.json();
                console.log('API response received', data);

                loadingDiv.classList.add('hidden');
                resultDiv.classList.remove('hidden');
                sendBtn.disabled = false;

                // Verwerk de response met DOM manipulatie
                processApiResponse(data);

            } catch (error) {
                console.error('Fout bij API call:', error);
                showError('API Fout', error.message);
                sendBtn.disabled = false;
            }
        }, 'image/jpeg', 0.8);

    } catch (error) {
        console.error('Fout:', error);
        showError('Er ging iets mis', error.message);
        sendBtn.disabled = false;
    }
});

// Hulpfunctie: Converteer blob naar base64
function blobToBase64(blob) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(blob);
    });
}

// Verwerk API response met DOM manipulatie
function processApiResponse(data) {
    clearResultDiv();

    if (data.result && data.result.classification && data.result.classification.suggestions) {
        const suggestions = data.result.classification.suggestions;

        if (suggestions.length > 0) {
            const suggestion = suggestions[0];

            // Creëer resultaat elementen met DOM manipulatie
            createResultElements(suggestion, data);
        } else {
            throw new Error('Geen plant herkend in de foto');
        }
    } else {
        throw new Error('Ongeldige response van Plant.id API');
    }
}

// Creëer resultaat elementen met DOM manipulatie
function createResultElements(suggestion, data) {
    // Hoofd titel
    const title = document.createElement('h2');
    title.textContent = ' Scan Resultaat';
    resultDiv.appendChild(title);

    // Plant info container
    const plantInfo = document.createElement('div');
    plantInfo.className = 'plant-info';
    resultDiv.appendChild(plantInfo);

    // Wetenschappelijke naam
    const scientificName = document.createElement('p');
    scientificName.innerHTML = '<strong> Wetenschappelijke naam:</strong> ' + (suggestion.name || 'Onbekend');
    plantInfo.appendChild(scientificName);

    // Nauwkeurigheid
    const accuracy = document.createElement('p');
    const probability = suggestion.probability || 0;
    accuracy.innerHTML = '<strong> Nauwkeurigheid:</strong> ' + Math.round(probability * 100) + '%';
    plantInfo.appendChild(accuracy);

    // Nederlandse naam
    const commonNames = document.createElement('p');
    let commonNamesText = 'Onbekend';
    if (suggestion.details && suggestion.details.common_names) {
        commonNamesText = suggestion.details.common_names.join(', ');
    }
    commonNames.innerHTML = '<strong>🇳🇱 Nederlandse naam:</strong> ' + commonNamesText;
    plantInfo.appendChild(commonNames);

    // Gezondheidsinfo
    if (data.result.disease && data.result.disease.suggestions && data.result.disease.suggestions.length > 0) {
        const disease = data.result.disease.suggestions[0];
        const health = document.createElement('p');
        health.innerHTML = '<strong> Gezondheid:</strong> ' + (disease.name || 'Onbekend') + ' (' + Math.round((disease.probability || 0) * 100) + '%)';
        plantInfo.appendChild(health);
    }

    // Beschrijving
    if (suggestion.details && suggestion.details.description && suggestion.details.description.value) {
        const description = document.createElement('p');
        const descText = suggestion.details.description.value.substring(0, 250) + '...';
        description.innerHTML = '<strong> Beschrijving:</strong><br>' + descText;
        plantInfo.appendChild(description);
    }

    // Vergelijkbare afbeeldingen
    if (suggestion.similar_images && suggestion.similar_images.length > 0) {
        const similarContainer = document.createElement('div');
        similarContainer.className = 'similar-images';

        const similarTitle = document.createElement('p');
        similarTitle.innerHTML = '<strong> Vergelijkbare afbeeldingen:</strong>';
        similarContainer.appendChild(similarTitle);

        suggestion.similar_images.slice(0, 5).forEach(img => {
            const imgElement = document.createElement('img');
            imgElement.src = img.url;
            imgElement.className = 'similar-image';
            imgElement.title = img.citation || 'Similar image';
            imgElement.onclick = () => window.open(img.url, '_blank');
            similarContainer.appendChild(imgElement);
        });

        plantInfo.appendChild(similarContainer);
    }

    // Nieuwe scan knop
    const newScanBtn = document.createElement('button');
    newScanBtn.textContent = ' Nieuwe scan';
    newScanBtn.className = 'btn';
    newScanBtn.onclick = () => location.reload();
    resultDiv.appendChild(newScanBtn);
}

// Toon error message met DOM manipulatie
function showError(title, message) {
    loadingDiv.classList.add('hidden');
    resultDiv.classList.remove('hidden');

    clearResultDiv();

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error';

    const errorTitle = document.createElement('h3');
    errorTitle.textContent =  title;
    errorDiv.appendChild(errorTitle);

    const errorMsg = document.createElement('p');
    errorMsg.textContent = message;
    errorDiv.appendChild(errorMsg);

    const errorHint = document.createElement('p');
    errorHint.innerHTML = '<small>Controleer je API key en internet verbinding</small>';
    errorDiv.appendChild(errorHint);

    const retryBtn = document.createElement('button');
    retryBtn.textContent = ' Probeer opnieuw';
    retryBtn.className = 'btn';
    retryBtn.onclick = () => location.reload();
    errorDiv.appendChild(retryBtn);

    resultDiv.appendChild(errorDiv);
}

// Leeg de result div
function clearResultDiv() {
    while (resultDiv.firstChild) {
        resultDiv.removeChild(resultDiv.firstChild);
    }
}

// Start de camera wanneer de pagina laadt
window.addEventListener('load', function() {
    if (window.location.protocol !== 'https:') {
        showError('HTTPS Vereist', 'Deze website vereist HTTPS voor camera toegang. Gelieve te gebruiken via HTTPS.');
    }

    if (!PLANT_ID_API_KEY || PLANT_ID_API_KEY === 'Tzjm3d6QtmenotzI7SZjpPyrZUXm41gZF1xuc1ixBKEc6qk1gK') {
        showError('API Key Mist', 'Vergeet niet je Plant.id API key in config.js in te vullen!');
    }

    startCamera();
});