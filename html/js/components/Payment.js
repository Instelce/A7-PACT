const paymentIsChecked = document.getElementById("check-payment");
const divPayment = document.getElementById("mean-payment");

if (paymentIsChecked && divPayment) {
    paymentIsChecked.addEventListener('change', () => {
        divPayment.classList.toggle('hidden');
    });
}


const payment = document.getElementById("payment");
const contentPayment = document.getElementById("content-payment");

const card = document.getElementById("card");
const contentCard = document.getElementById("content-card");


card.addEventListener("click", ()=> {
    contentCard.classList.toggle('hidden');
    contentPayment.classList.add('hidden');
});

payment.addEventListener("click", ()=> {
    console.log("coucuo")
    contentPayment.classList.toggle('hidden');
    contentCard.classList.add('hidden');
});
