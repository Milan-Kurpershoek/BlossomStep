// ================= CONFIGURATIE =================
// VERVANG DIT MET JE EIGEN PLANT.ID API KEY!
const PLANT_ID_API_KEY = 'Tzjm3d6QtmenotzI7SZjpPyrZUXm41gZF1xuc1ixBKEc6qk1gK'; // ← HAAL VAN https://web.plant.id/
const PLANT_ID_API_URL = 'https://plant.id/api/v3/identification';
// ================================================

console.log('Config loaded: API key set to', PLANT_ID_API_KEY ? '***' + PLANT_ID_API_KEY.slice(-4) : 'NOT SET');