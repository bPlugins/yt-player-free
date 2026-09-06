
export function convertBooleansInObject(obj) {
    if (typeof obj !== "object" || obj === null) return convertBoolean(obj);

    if (Array.isArray(obj)) {
        return obj.map(convertBooleansInObject);
    }

    return Object.fromEntries(
        Object.entries(obj).map(([key, value]) => [key, convertBooleansInObject(value)])
    );
}

export function convertBoolean(value) {
    if (value === "true") return true;
    if (value === "false") return false;
    return value; // Return original value if it's not a boolean string
}

export const pricingPage = `${location.origin}/wp-admin/edit.php?post_type=ytplayer&page=dashboard#/pricing`;