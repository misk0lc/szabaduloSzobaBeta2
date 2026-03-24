export interface User {
  UserID: number;
  Username: string;
  Email: string;
  IsAdmin: boolean;
  IsActive: boolean;
  CreatedAt?: string;
  Balance?: number;
}

export interface AuthResponse {
  message: string;
  user: User;
  token: string;
}

export interface LoginRequest {
  Email: string;
  Password: string;
}

export interface RegisterRequest {
  Username: string;
  Email: string;
  Password: string;
  Password_confirmation: string;
}
