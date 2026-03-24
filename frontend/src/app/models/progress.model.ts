export interface SubmitCodeRequest {
  code: string;
  timeSpent: number;
}

export interface SubmitCodeResponse {
  correct: boolean;
  message: string;
  Score?: number;
  TimeSpent?: number;
  CompletedAt?: string;
  TotalScore?: number;
  LevelsCompleted?: number;
  NextLevel?: {
    LevelID: number;
    Name: string;
    OrderNumber: number;
  };
}
