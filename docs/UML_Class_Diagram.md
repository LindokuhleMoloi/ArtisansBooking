# UML Class Diagram (Core Classes)

```
┌─────────────────────┐
│      Database       │
│  (Singleton PDO)    │
└──────────┬──────────┘
           │ used by
┌──────────▼──────────┐       ┌─────────────────────┐
│        Auth         │       │       Router        │
│  - login()          │       │  - dispatch()       │
│  - logout()         │       └─────────────────────┘
│  - check() / role() │
└─────────────────────┘

┌─────────────────────┐       ┌─────────────────────┐
│        User         │       │      Category       │
│  - findByEmail()    │       │  - all()            │
│  - findById()       │       │  - findById()       │
│  - create()         │       └─────────────────────┘
│  - getArtisans()    │
└──────────┬──────────┘
           │ 1
           │
           │ 0..1
┌──────────▼──────────┐
│  ArtisanProfile     │  (table / concept)
│  business_name      │
│  avg_rating         │
│  total_bookings     │
│  is_verified        │
└─────────────────────┘

┌─────────────────────┐       ┌─────────────────────┐
│       Service       │──────▶│      Category       │
│  - findById()       │  N:1  └─────────────────────┘
│  - getByArtisan()   │
│  - getAllActive()   │
└──────────┬──────────┘
           │ 1
           │
           │ N
┌──────────▼──────────┐
│       Booking       │
│  - create()         │
│  - findById()       │
│  - getByCustomer()  │
│  - getByArtisan()   │
│  - updateStatus()   │
│  - isSlotAvailable()│
└──────────┬──────────┘
           │ 1
           │
           │ 0..1
┌──────────▼──────────┐
│       Review        │
│  - create()         │
│  - recalculate...() │
│  - getByArtisan()   │
└─────────────────────┘

┌─────────────────────────────┐
│   RecommendationEngine      │
│  - recommend(userId, limit) │
│  - logRecommendations()     │
└─────────────────────────────┘

Controllers (depend on Models + Auth + Views):
  HomeController, AuthController, ArtisanController,
  BookingController, RecommendationController,
  DashboardController, ReviewController
```

## Relationships (summary)

| From              | To                 | Cardinality | Notes                          |
|-------------------|--------------------|-------------|--------------------------------|
| User              | ArtisanProfile     | 1 : 0..1    | Only for role = artisan        |
| User (artisan)    | Service            | 1 : 0..*    | Offers services                |
| Category          | Service            | 1 : 0..*    | Classification                 |
| User (customer)   | Booking            | 1 : 0..*    | Places bookings                |
| User (artisan)    | Booking            | 1 : 0..*    | Receives bookings              |
| Service           | Booking            | 1 : 0..*    | Booked service                 |
| Booking           | Review             | 1 : 0..1    | One review per completed booking |
