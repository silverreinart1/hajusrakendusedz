# Hajusrakendused

Laravel 11 + Vue 3 + Inertia.js projekt, mis lahendab kursuse viis ülesannet ühe tervikliku rakendusena.

---

## Ülesehitus

```
hajusrakendused/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── WeatherController.php     # Ülesanne 1 – Ilm
│   │   │   ├── MapController.php         # Ülesanne 2 – Kaart
│   │   │   ├── BlogController.php        # Ülesanne 3 – Blogi
│   │   │   ├── CommentController.php     # Ülesanne 3 – Kommentaarid
│   │   │   ├── ShopController.php        # Ülesanne 4 – E-pood (tooted)
│   │   │   ├── CartController.php        # Ülesanne 4 – Ostukorv
│   │   │   ├── CheckoutController.php    # Ülesanne 4 – Stripe makse
│   │   │   ├── AuthController.php        # Auth
│   │   │   └── Api/BookController.php    # Ülesanne 5 – Raamatud API
│   │   └── Middleware/HandleInertiaRequests.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Marker.php
│   │   ├── Post.php
│   │   ├── Comment.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   └── Book.php                      # tabel: my_favorite_subject
│   ├── Policies/PostPolicy.php
│   └── Services/WeatherService.php
├── database/
│   ├── migrations/                       # Üks fail, kõik tabelid
│   └── seeders/DatabaseSeeder.php        # 5 raamatut näidisandmetega
├── resources/
│   ├── css/app.css                       # Globaalne CSS + disainisüsteem
│   ├── js/
│   │   ├── app.js                        # Inertia bootstrap
│   │   ├── Layouts/AppLayout.vue         # Ühine navigatsioon + jalus
│   │   └── Pages/
│   │       ├── Home.vue
│   │       ├── Auth/{Login,Register}.vue
│   │       ├── Weather/Index.vue
│   │       ├── Map/Index.vue
│   │       ├── Blog/{Index,Show,Create,Edit}.vue
│   │       ├── Shop/{Index,Cart,Checkout,Success,Cancel}.vue
│   │       └── Books/Index.vue
│   └── views/app.blade.php               # Inertia juurmall
├── routes/
│   ├── web.php                           # Kõik veebiteed
│   └── api.php                           # /api/books
└── docs/                                 # See fail
```

---

## Kasutatud tehnoloogiad

| Kiht | Tehnoloogia |
|------|-------------|
| Backend raamistik | Laravel 11 (PHP 8.3) |
| Frontend raamistik | Vue 3 (Composition API) |
| SPA sild | Inertia.js v1 |
| Ehitusvahend | Vite |
| CSS | Tailwind CSS 3 |
| Andmebaas | SQLite (arendus) / MySQL (tootmine) |
| Ilma API | OpenWeatherMap (`/data/2.5/weather`) |
| Kaart | Leaflet.js + OpenStreetMap |
| Makse | Stripe Checkout Sessions |
| Vahemälu | Laravel Cache (database driver) |
| Versioonihaldus | Git / GitHub |

---

## Andmebaasitabelid

| Tabel | Kirjeldus |
|-------|-----------|
| `users` | Kasutajad (auth) |
| `sessions` | Sessiooniandmed |
| `cache` | Laravel cache |
| `markers` | Kaardi markerid (ülesanne 2) |
| `posts` | Blogi postitused (ülesanne 3) |
| `comments` | Postituste kommentaarid (ülesanne 3) |
| `orders` | Tellimused (ülesanne 4) |
| `order_items` | Tellimusread (ülesanne 4) |
| `my_favorite_subject` | Raamatud API (ülesanne 5) |

---

## Paigaldamine ja käivitamine

### Eeltingimused
- PHP 8.3+
- Composer
- Node.js 20+
- SQLite (arenduseks) või MySQL

### Sammud

```bash
# 1. Klooni repositoorium
git clone https://github.com/KASUTAJANIMI/hajusrakendused.git
cd hajusrakendused

# 2. Installi PHP sõltuvused
composer install

# 3. Installi JS sõltuvused
npm install

# 4. Loo .env fail
cp .env.example .env
php artisan key:generate

# 5. Konfigureeri .env
# Lisa oma võtmed:
#   OPENWEATHER_API_KEY=...
#   STRIPE_KEY=...
#   STRIPE_SECRET=...
#   STRIPE_WEBHOOK_SECRET=...

# 6. Loo andmebaas + migratsioonid + näidisandmed
touch database/database.sqlite
php artisan migrate --seed

# 7. Ehita frontend
npm run build
# või arenduseks:
npm run dev

# 8. Käivita server
php artisan serve
```

Rakendus on nüüd saadaval aadressil **http://localhost:8000**

---

## API dokumentatsioon (Ülesanne 5)

### Raamatud API

Base URL: `/api/books`

#### `GET /api/books`

Tagastab raamatute loendi. Toetab järgmisi päringuparam:

| Parameeter | Tüüp | Vaikimisi | Kirjeldus |
|------------|------|-----------|-----------|
| `search` | string | – | Otsi pealkirja või autori järgi |
| `genre` | string | – | Filtreeri žanri järgi |
| `sort` | string | `id` | Sorteeriväli: `title`, `author`, `publication_year`, `id` |
| `order` | string | `asc` | Järjestus: `asc` või `desc` |
| `limit` | integer | `20` | Maks kirjete arv (1–100) |

**Näide:**
```
GET /api/books?search=tolkien&sort=publication_year&order=desc&limit=5
```

**Vastus:**
```json
{
  "data": [
    {
      "id": 3,
      "title": "The Lord of the Rings",
      "author": "J. R. R. Tolkien",
      "publication_year": 1954,
      "genre": "Fantaasia",
      "description": "...",
      "image": null,
      "isbn": "978-0261102385",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "meta": {
    "count": 1,
    "limit": 5,
    "sort": "publication_year",
    "order": "desc",
    "search": "tolkien",
    "genre": null
  }
}
```

#### `GET /api/books/{id}` — Üks raamat
#### `POST /api/books` — Lisa raamat (JSON keha: `title`, `author`, `publication_year`, `description` kohustuslikud)
#### `PUT /api/books/{id}` — Uuenda raamatut
#### `DELETE /api/books/{id}` — Kustuta raamat

Vastused vahemälus **5 minutit**. Lisamine/muutmine/kustutamine tühjendab vahemälu automaatselt.

---

## Stripe testimine

Testi makseteks kasuta Stripe'i testkaarti:

- **Kaardi number:** `4242 4242 4242 4242`
- **Aegumiskuupäev:** mis tahes tuleviku kuupäev (nt `12/34`)
- **CVC:** mis tahes 3 numbrit (nt `123`)

Stripe webhook lokaalseks testimiseks:
```bash
stripe listen --forward-to localhost:8000/stripe/webhook
```

---

## Tootmisse paigaldamine (Zone)

1. Laadi failid FTP kaudu üles (v.a `node_modules`, `vendor`)
2. SSH kaudu:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm run build
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
3. Veendu, et `.env` on konfigureeritud tootmisandmetega
4. `APP_ENV=production`, `APP_DEBUG=false`
