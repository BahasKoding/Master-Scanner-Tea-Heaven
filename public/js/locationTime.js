function getCurrentTimeAndLocation() {
    return new Promise((resolve, reject) => {
        const currentTime = new Date().toISOString();

        if (!navigator.geolocation) {
            reject(new Error("Geolocation tidak didukung oleh browser ini."));
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                resolve({
                    time: currentTime,
                    latitude: latitude,
                    longitude: longitude
                });
            },
            (error) => {
                console.error("Error mendapatkan lokasi:", error.message);
                reject(error);
            }
        );
    });
}

// Penggunaan:
async function getLocationData() {
    try {
        const result = await getCurrentTimeAndLocation();
        console.log(result);
        return result;
    } catch (error) {
        console.error("Terjadi kesalahan:", error);
    }
}
