
class Signal {

    constructor(value) {
        this.value = value;
        this.subscribers = [];
    }

    getValue() {
        return this.value;
    }

    setValue(value) {
        this.value = value;
        this.emit();
    }

    emit() {
        this.subscribers.forEach(subscriber => subscriber(this.value));
    }

    subscribe(callback) {
        this.subscribers.push(callback);
    }
}


export function createSignal(value) {
    const signal = new Signal(value);

    return [
        function value() {
            return signal.getValue();
        },
        function setValue(value) {
            signal.setValue(value);
        }
    ]
}
