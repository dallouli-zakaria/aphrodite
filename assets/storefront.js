let cartKey = "aphroditeshop-cart-v3";
let themeKey = "aphroditeshop-theme-v3";
let checkoutUrl = "/checkout";
let cartAddUrl = "/cart/add";
let cartMergeUrl = "/cart/merge";
let cartRemoveUrl = "/cart/remove";
let cartCsrfToken = "";
let orderCreateUrl = "/checkout/order";
let orderCsrfToken = "";
let isAuthenticated = false;
let accountCart = { items: [], count: 0, subtotal: 0, delivery: 0, total: 0 };
let freeShippingLimit = 599;
let shippingCost = 29;
let toastTimer;
let initializedConfigNode = null;

const money = (value) => `${Number(value).toLocaleString("fr-FR")} MAD`;
const readLocalCart = () => JSON.parse(localStorage.getItem(cartKey) || "[]");
const writeLocalCart = (cart) => localStorage.setItem(cartKey, JSON.stringify(cart));
const readCart = () => isAuthenticated ? (accountCart.items || []) : readLocalCart();
const writeCart = (cart) => {
  if (isAuthenticated) accountCart = { ...accountCart, items: cart };
  else writeLocalCart(cart);
};

function refreshConfig() {
  const configNode = document.querySelector("[data-storefront-config]");
  const config = JSON.parse(configNode?.textContent || "{}");

  cartKey = config.cartKey || cartKey;
  themeKey = config.themeKey || themeKey;
  checkoutUrl = config.checkoutUrl || checkoutUrl;
  cartAddUrl = config.cartAddUrl || cartAddUrl;
  cartMergeUrl = config.cartMergeUrl || cartMergeUrl;
  cartRemoveUrl = config.cartRemoveUrl || cartRemoveUrl;
  cartCsrfToken = config.cartCsrfToken || cartCsrfToken;
  orderCreateUrl = config.orderCreateUrl || orderCreateUrl;
  orderCsrfToken = config.orderCsrfToken || orderCsrfToken;
  isAuthenticated = Boolean(config.isAuthenticated);
  accountCart = config.accountCart || accountCart;
  freeShippingLimit = Number(config.freeShippingLimit || freeShippingLimit);
  shippingCost = Number(config.shippingCost || shippingCost);
}

function updateCartCount() {
  const count = isAuthenticated ? accountCart.count : readCart().reduce((total, item) => total + item.qty, 0);
  document.querySelectorAll("[data-cart-count]").forEach((node) => node.textContent = count);
}

function cartTotals(cart = readCart()) {
  const subtotal = cart.reduce((total, item) => total + item.price * item.qty, 0);
  const delivery = subtotal === 0 || subtotal >= freeShippingLimit ? 0 : shippingCost;

  return { subtotal, delivery, total: subtotal + delivery };
}

function showToast(message) {
  const toast = document.querySelector("[data-toast]");
  if (!toast) return;
  toast.textContent = message;
  toast.classList.add("is-visible");
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => toast.classList.remove("is-visible"), 2600);
}

function openCartDrawer() {
  document.querySelector("[data-cart-overlay]")?.classList.add("is-open");
  document.querySelector("[data-cart-drawer]")?.classList.add("is-open");
  document.querySelector("[data-cart-drawer]")?.setAttribute("aria-hidden", "false");
  renderDrawerCart();
}

function closeCartDrawer() {
  document.querySelector("[data-cart-overlay]")?.classList.remove("is-open");
  document.querySelector("[data-cart-drawer]")?.classList.remove("is-open");
  document.querySelector("[data-cart-drawer]")?.setAttribute("aria-hidden", "true");
}

function closeMobileNav() {
  document.body.classList.remove("nav-is-open");
  document.querySelector("[data-mobile-nav-toggle]")?.setAttribute("aria-expanded", "false");
}

function toggleMobileNav() {
  const isOpen = !document.body.classList.contains("nav-is-open");
  document.body.classList.toggle("nav-is-open", isOpen);
  document.querySelector("[data-mobile-nav-toggle]")?.setAttribute("aria-expanded", isOpen ? "true" : "false");
}

function productScope(button) {
  return button.closest(".product-card, .product-detail-info");
}

function selectedSize(button) {
  return productScope(button)?.querySelector("[data-size-option].is-selected")?.dataset.sizeOption || "";
}

function showMissingSize(button) {
  const group = productScope(button)?.querySelector("[data-size-group]");
  group?.classList.add("is-missing");
  setTimeout(() => group?.classList.remove("is-missing"), 500);
  showToast("Veuillez choisir une taille avant d'ajouter au panier.");
}

async function postCart(url, payload) {
  const response = await fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": cartCsrfToken,
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify(payload),
  });

  if (!response.ok) {
    let message = "Le panier n'a pas pu etre synchronise.";
    try {
      message = (await response.json()).message || message;
    } catch (error) {
      // Keep the default message when the response is not JSON.
    }
    throw new Error(message);
  }

  accountCart = await response.json();
  updateCartCount();
  renderDrawerCart();
  renderCheckout();

  return accountCart;
}

async function syncGuestCartToServer() {
  if (!isAuthenticated) return;
  const guestCart = readLocalCart();
  if (!guestCart.length) return;

  try {
    await postCart(cartMergeUrl, { items: guestCart });
    localStorage.removeItem(cartKey);
  } catch (error) {
    console.warn(error);
  }
}

async function addProduct(button, redirect = false) {
  const product = {
    id: Number(button.dataset.id),
    name: button.dataset.name,
    price: Number(button.dataset.price),
    image: button.dataset.image,
    size: selectedSize(button),
  };

  if (!product.size) {
    showMissingSize(button);
    return false;
  }

  if (isAuthenticated) {
    try {
      await postCart(cartAddUrl, { productId: product.id, size: product.size });
      showToast(`${product.name} a ete ajoute au panier.`);
      if (redirect) window.location.href = checkoutUrl;
      return true;
    } catch (error) {
      showToast(error.message || "Impossible de mettre a jour le panier. Reessayez.");
      return false;
    }
  }

  const cart = readCart();
  const existing = cart.find((item) => item.id === product.id && item.size === product.size);
  if (existing) existing.qty += 1;
  else cart.push({ ...product, key: `${product.id}:${product.size}`, qty: 1 });
  writeCart(cart);
  updateCartCount();
  renderDrawerCart();
  renderCheckout();
  showToast(`${product.name} a ete ajoute au panier.`);
  if (redirect) window.location.href = checkoutUrl;
  return true;
}

async function removeProduct(productId, size) {
  if (isAuthenticated) {
    try {
      await postCart(cartRemoveUrl, { productId, size });
    } catch (error) {
      showToast("Impossible de retirer cet article. Reessayez.");
    }

    return;
  }

  writeCart(readCart().filter((item) => !(item.id === productId && item.size === size)));
  updateCartCount();
  renderDrawerCart();
  renderCheckout();
}

function renderCartLines(target, cart) {
  if (!cart.length) {
    target.innerHTML = `<div class="empty">Votre panier est vide.</div>`;
    return;
  }

  target.innerHTML = cart.map((item) => `
    <div class="cart-line">
      <img src="${item.image}" alt="${item.name}">
      <div>
        <strong>${item.name}</strong>
        <div>${money(item.price)} x ${item.qty}</div>
        <small>${item.size ? `Taille: ${item.size}` : "Taille manquante"}</small>
      </div>
      <button class="button ghost" type="button" data-remove="${item.id}" data-remove-size="${item.size || ""}">X</button>
    </div>
  `).join("");
}

function renderDrawerCart() {
  const target = document.querySelector("[data-drawer-cart]");
  if (!target) return;
  const cart = readCart();
  renderCartLines(target, cart);
  document.querySelector("[data-drawer-total]")?.replaceChildren(money(cartTotals(cart).total));
}

function renderCheckout() {
  const target = document.querySelector("[data-checkout-cart]");
  if (!target) return;
  const cart = readCart();
  const totals = cartTotals(cart);
  renderCartLines(target, cart);
  document.querySelector("[data-subtotal]")?.replaceChildren(money(totals.subtotal));
  document.querySelector("[data-delivery]")?.replaceChildren(money(totals.delivery));
  document.querySelector("[data-total]")?.replaceChildren(money(totals.total));
}

function applyTheme(choice) {
  const resolved = choice === "dark" ? "dark" : "light";
  document.body.dataset.resolvedTheme = resolved;
  localStorage.setItem(themeKey, resolved);
  document.querySelectorAll("[data-theme-label]").forEach((node) => {
    node.textContent = resolved === "dark" ? "Sombre" : "Clair";
  });
}

document.addEventListener("click", async (event) => {
  const sizeButton = event.target.closest("[data-size-option]");
  if (sizeButton) {
    const group = sizeButton.closest("[data-size-group]");
    group?.classList.remove("is-missing");
    group?.querySelectorAll("[data-size-option]").forEach((button) => {
      const isSelected = button === sizeButton;
      button.classList.toggle("is-selected", isSelected);
      button.setAttribute("aria-pressed", isSelected ? "true" : "false");
    });
  }

  const addButton = event.target.closest("[data-add-product]");
  if (addButton) {
    if (await addProduct(addButton)) openCartDrawer();
  }

  const buyButton = event.target.closest("[data-buy-product]");
  if (buyButton) await addProduct(buyButton, true);

  const removeButton = event.target.closest("[data-remove]");
  if (removeButton) {
    await removeProduct(Number(removeButton.dataset.remove), removeButton.dataset.removeSize || "");
  }

  if (event.target.closest("[data-cart-open]")) {
    openCartDrawer();
  }

  if (event.target.closest("[data-cart-close]") || event.target.closest("[data-cart-overlay]")) {
    closeCartDrawer();
  }

  if (event.target.closest("[data-theme-toggle]")) {
    applyTheme(document.body.dataset.resolvedTheme === "dark" ? "light" : "dark");
  }

  if (event.target.closest("[data-mobile-nav-toggle]")) {
    toggleMobileNav();
  }

  if (event.target.closest(".nav a")) {
    closeMobileNav();
  }
});

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    closeCartDrawer();
    closeMobileNav();
  }
});

document.addEventListener("submit", (event) => {
  if (event.target.closest("[data-checkout-form]")) {
    handleCheckoutSubmit(event.target, event);
  }
});

async function handleCheckoutSubmit(form, event) {
  event.preventDefault();
  const cart = readCart();
  if (!cart.length) {
    showToast("Votre panier est vide.");
    return;
  }
  if (cart.some((item) => !item.size)) {
    showToast("Veuillez choisir une taille pour chaque article avant de commander.");
    return;
  }

  const submitButton = form.querySelector("[type='submit']");
  submitButton?.setAttribute("disabled", "disabled");

  try {
    const formData = new FormData(form);
    const response = await fetch(orderCreateUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": orderCsrfToken,
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        customer: Object.fromEntries(formData.entries()),
        items: cart,
      }),
    });

    const data = await response.json();
    if (!response.ok) {
      throw new Error(data.message || "Impossible de valider la commande.");
    }

    localStorage.removeItem(cartKey);
    accountCart = { items: [], count: 0, subtotal: 0, delivery: 0, total: 0 };
    updateCartCount();
    renderCheckout();
    renderDrawerCart();
    window.location.href = data.redirectUrl;
  } catch (error) {
    showToast(error.message || "Impossible de valider la commande.");
    submitButton?.removeAttribute("disabled");
  }
}

function initializeStorefront() {
  const configNode = document.querySelector("[data-storefront-config]");
  if (!configNode || configNode === initializedConfigNode) return;
  initializedConfigNode = configNode;

  refreshConfig();
  applyTheme(localStorage.getItem(themeKey) || "light");
  closeMobileNav();
  updateCartCount();
  renderDrawerCart();
  renderCheckout();
  syncGuestCartToServer();

  const productDetails = document.querySelector("[data-product-details]");
  if (productDetails) {
    requestAnimationFrame(() => {
      productDetails.scrollIntoView({ behavior: "smooth", block: "center" });
    });
  }
}

document.addEventListener("DOMContentLoaded", initializeStorefront);
document.addEventListener("turbo:load", initializeStorefront);
