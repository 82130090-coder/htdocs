document.getElementById('bookingTime').addEventListener('change', function() {
    const selectedTime = new Date(this.value);
    const startTime = new Date(selectedTime);
    const endTime = new Date(selectedTime);
    startTime.setHours(10, 0, 0); 
    endTime.setHours(22, 0, 0); 

    if (selectedTime < startTime || selectedTime > endTime) {
        alert("Please select a time between 10 AM and 10 PM.");
        this.value = "";
    }
});