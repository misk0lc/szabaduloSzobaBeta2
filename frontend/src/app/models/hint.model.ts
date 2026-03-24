export interface Hint {
  HintID: number;
  HintOrder: number;
  Cost: number;
  HintText?: string;
}

export interface BuyHintResponse {
  message: string;
  HintID: number;
  HintOrder: number;
  HintText: string;
  Cost: number;
  NewBalance: number;
  HintsUsed: number;
}
