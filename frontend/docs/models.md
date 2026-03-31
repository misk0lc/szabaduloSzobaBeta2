# Modellek és interfészek

## Tartalomjegyzék

- [User](#user)
- [Level](#level)
- [Question](#question)
- [Hint](#hint)
- [Progress](#progress)
- [Leaderboard](#leaderboard)

---

## User

**Fájl:** `src/app/models/user.model.ts`

### User

```typescript
interface User {
  UserID: number;
  Username: string;
  Email: string;
  IsAdmin: boolean;
  IsActive: boolean;
  CreatedAt?: string;
  Balance?: number;
}
```

### AuthResponse

```typescript
interface AuthResponse {
  message: string;
  user: User;
  token: string;
}
```

### LoginRequest

```typescript
interface LoginRequest {
  Email: string;
  Password: string;
}
```

### RegisterRequest

```typescript
interface RegisterRequest {
  Username: string;
  Email: string;
  Password: string;
  Password_confirmation: string;
}
```

---

## Level

**Fájl:** `src/app/models/level.model.ts`

### Level

```typescript
interface Level {
  LevelID: number;
  Name: string;
  Description: string;
  Category: string;       // 'Könnyed' | 'Közepes' | 'Nehéz'
  OrderNumber: number;
  IsUnlocked: boolean;
  IsCompleted: boolean;
  IsActive: boolean;
  BackgroundUrl?: string | null;
}
```

### LevelDetail

```typescript
interface LevelDetail extends Level {
  TimeSpent: number;      // Eddigi eltelt idő másodpercben
  CompletedAt?: string;
}
```

---

## Question

**Fájl:** `src/app/models/question.model.ts`

### Question

```typescript
interface Question {
  QuestionID: number;
  LevelID: number;
  QuestionText: string;
  PositionX: number;      // 1-20, rácsozati pozíció
  PositionY: number;      // 1-4, rácsozati pozíció
  MoneyReward: number;
  Solved?: boolean;
  RewardDigit?: number;   // 0-9, csak megoldottaknál
  Options?: {
    OptionID: number;
    OptionText: string;
    IsCorrect: boolean;
  }[];
}
```

### CheckAnswerRequest

```typescript
interface CheckAnswerRequest {
  answer: string;
}
```

### CheckAnswerResponse

```typescript
interface CheckAnswerResponse {
  correct: boolean;
  message: string;
  RewardDigit?: number;    // Helyes válasznál
  MoneyReward?: number;    // Helyes válasznál
  NewBalance?: number;     // Frissített egyenleg
  MoneyPenalty?: number;   // Hibás 1. válasznál
  TimePenalty?: number;    // Hibás 2+ válasznál (mp-ben)
  WrongCount?: number;     // Hibás válaszok száma összesen
  Penalty?: number;        // Általános büntetés értéke
}
```

---

## Hint

**Fájl:** `src/app/models/hint.model.ts`

### Hint

```typescript
interface Hint {
  HintID: number;
  HintOrder: number;
  Cost: number;
  HintText?: string;      // Csak megvásárlás után
}
```

### BuyHintResponse

```typescript
interface BuyHintResponse {
  message: string;
  HintID: number;
  HintOrder: number;
  HintText: string;
  Cost: number;
  NewBalance: number;
  HintsUsed: number;
}
```

---

## Progress

**Fájl:** `src/app/models/progress.model.ts`

### SubmitCodeRequest

```typescript
interface SubmitCodeRequest {
  code: string;           // A szobakód (számjegyek összefűzve)
  timeSpent: number;      // Eltelt idő másodpercben
}
```

### SubmitCodeResponse

```typescript
interface SubmitCodeResponse {
  correct: boolean;
  message: string;
  Score?: number;          // Szobáért kapott pontszám
  TimeSpent?: number;      // Végső idő
  CompletedAt?: string;
  TotalScore?: number;     // Kumulatív összpontszám
  LevelsCompleted?: number;
  NextLevel?: {
    LevelID: number;
    Name: string;
    OrderNumber: number;
  };
}
```

---

## Leaderboard

**Fájl:** `src/app/models/leaderboard.model.ts`

### LeaderboardEntry

```typescript
interface LeaderboardEntry {
  UserID: number;
  Username?: string;
  Score: number;
  LevelsCompleted: number;
  TimeTotal: number;       // Összidő másodpercben
  HintsUsed: number;
}
```
