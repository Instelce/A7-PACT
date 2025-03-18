
export function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

export function translateCategory(category) {
    switch (category) {
        case 'attraction_park':
            return 'parc d\'attraction';
        case 'visit':
            return 'visite';
        case 'restaurant':
            return 'restaurant';
        case 'activity':
            return 'activité';
        case 'show':
            return 'spectacle';
    }
}