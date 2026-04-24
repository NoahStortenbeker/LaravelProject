import { menData, menSubData } from "./menData.js"
import { womenData, womenSubData } from "./womenData.js"
import { kidsData, kidsSubData } from "./kidsData.js"

// Base sets
const MEN_APPAREL = ["XS","S","M","L","XL","XXL"]
const WOMEN_APPAREL = ["XXS","XS","S","M","L","XL"]
const MEN_PANTS = Array.from({ length: 13 }, (_, i) => String(28 + i)) // 28–40
const WOMEN_JEANS = Array.from({ length: 9 }, (_, i) => String(24 + i)) // 24–32
const MEN_SHOES_EU = Array.from({ length: 9 }, (_, i) => String(39 + i)) // 39–47
const WOMEN_SHOES_EU = Array.from({ length: 8 }, (_, i) => String(35 + i)) // 35–42
const KIDS_SHOES_EU = Array.from({ length: 8 }, (_, i) => String(28 + i)) // 28–35
const KIDS_APPAREL = ["2Y","3Y","4Y","5Y","6Y","7Y","8Y","10Y","12Y","14Y"]
const BABY_MONTHS = ["0-3M","3-6M","6-9M","9-12M","12-18M","18-24M"]

function isSaleName(name = "") {
  const n = name.toUpperCase()
  return n.includes("SALE") || n.includes("DISCOUNT")
}
function buildGroup(topData, subMap) {
  return topData
    .filter(t => !t.isSale)
    .map(t => {
      const subs = (t.target && subMap[t.target]) ? subMap[t.target].filter(s => !isSaleName(s.name)) : []
      return { name: t.name, target: t.target || null, subs }
    })
}
export const groupedCategories = [
  { group: "MEN", items: buildGroup(menData, menSubData) },
  { group: "WOMEN", items: buildGroup(womenData, womenSubData) },
  { group: "KIDS", items: buildGroup(kidsData, kidsSubData) },
]

export function getSizesForCategory(name, group) {
  const n = (name || "").toUpperCase()
  const g = (group || "").toUpperCase()

  // MEN
  if (g === "MEN") {
    if (n.includes("SHOE") || n.includes("SNEAKER") || n.includes("BOOT") || n === "SHOES" || n.includes("RUNNING")) {
      return [{ label: "Men Shoes (EU)", type: "men_shoes_eu", values: MEN_SHOES_EU }]
    } else if (n.includes("JEANS") || n.includes("TROUSERS") || n.includes("CARGO") || n.includes("CARGO'S")) {
      return [{ label: "Men Pants (Waist)", type: "men_pants", values: MEN_PANTS }]
    } else if (n.includes("ACCESSORIES") || n.includes("BAGS") || n.includes("CAPS") || n.includes("WATCHES")) {
      return [{ label: "Other", type: "other", values: ["ONE SIZE"] }]
    } else {
      return [{ label: "Men Apparel", type: "men_apparel", values: MEN_APPAREL }]
    }
  }

  // WOMEN
  if (g === "WOMEN") {
    if (n.includes("SHOE") || n.includes("HEEL") || n.includes("SNEAKER") || n.includes("BOOT") || n === "SHOES" || n.includes("RUNNING SHOES") || n.includes("BIKE SHOES")) {
      return [{ label: "Women Shoes (EU)", type: "women_shoes_eu", values: WOMEN_SHOES_EU }]
    } else if (n.includes("JEANS")) {
      return [{ label: "Women Jeans (Waist)", type: "women_jeans", values: WOMEN_JEANS }]
    } else if (n.includes("ACCESSORIES") || n.includes("BAGS") || n.includes("HANDBAGS") || n.includes("JEWELRY") || n.includes("SCARVES") || n.includes("BEAUTY") || n.includes("BAGS & ACCESSORIES")) {
      return [{ label: "Other", type: "other", values: ["ONE SIZE"] }]
    } else {
      return [{ label: "Women Apparel", type: "women_apparel", values: WOMEN_APPAREL }]
    }
  }

  // KIDS
  if (g === "KIDS") {
    if (n.includes("SHOE") || n.includes("SNEAKER")) {
      return [{ label: "Kids Shoes (EU)", type: "kids_shoes_eu", values: KIDS_SHOES_EU }]
    } else if (n.includes("BABY")) {
      return [{ label: "Baby (Months)", type: "baby_months", values: BABY_MONTHS }]
    } else if (n.includes("ACCESSORIES") || n.includes("BAGS") || n.includes("HATS") || n.includes("SOCKS")) {
      return [{ label: "Other", type: "other", values: ["ONE SIZE"] }]
    } else {
      return [{ label: "Kids Apparel (Age)", type: "kids_apparel", values: KIDS_APPAREL }]
    }
  }

  // Fallback by name only (when group is unknown)
  if (n.includes("SHOE") || n.includes("SNEAKER") || n.includes("BOOT") || n.includes("HEEL") || n.includes("RUNNING SHOES") || n.includes("BIKE SHOES") || n.includes("ALL SHOES")) {
    return [{ label: "Shoes (EU)", type: "shoes_eu", values: MEN_SHOES_EU }]
  } else if (n.includes("JEANS") || n.includes("TROUSERS") || n.includes("CARGO") || n.includes("CARGO'S")) {
    return [{ label: "Pants", type: "pants", values: MEN_PANTS }]
  } else if (n.includes("BOYS") || n.includes("GIRLS") || n.includes("BABY") || n.includes("ALL BOYS") || n.includes("ALL GIRLS") || n.includes("ALL BABY")) {
    return [{ label: "Kids", type: "kids_apparel", values: KIDS_APPAREL }]
  } else if (n.includes("ACCESSORIES") || n.includes("BAGS") || n.includes("CAPS") || n.includes("WATCHES") || n.includes("HANDBAGS") || n.includes("JEWELRY") || n.includes("SCARVES") || n.includes("SOCKS") || n.includes("BAGS & ACCESSORIES") || n.includes("BEAUTY")) {
    return [{ label: "Other", type: "other", values: ["ONE SIZE"] }]
  } else {
    return [{ label: "Apparel", type: "apparel", values: MEN_APPAREL }]
  }
}
