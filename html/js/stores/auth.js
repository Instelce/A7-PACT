import {createSignal} from "../utils/signal";

const [pro, setPro] = createSignal(false);

/**
 * @return boolean
 */
export function isPro() {
    return pro();
}

export function setPro(value) {
    setPro(value);
}
