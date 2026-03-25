export interface Level {
  LevelID: number;
  Name: string;
  Description: string;
  Category: string;
  OrderNumber: number;
  IsUnlocked: boolean;
  IsCompleted: boolean;
  IsActive: boolean;
  BackgroundUrl?: string | null;
}

export interface LevelDetail extends Level {
  TimeSpent: number;
  CompletedAt?: string;
}
