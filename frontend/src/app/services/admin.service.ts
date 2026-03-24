import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface AdminUser {
  UserID: number;
  Username: string;
  Email: string;
  IsAdmin: boolean;
  IsActive: boolean;
  CreatedAt?: string;
  Balance: number;
  Score: number;
}

export interface AdminLevel {
  LevelID: number;
  Name: string;
  Description: string;
  OrderNumber: number;
  IsActive: boolean;
  BackgroundUrl?: string | null;
}

export interface AdminQuestion {
  QuestionID: number;
  LevelID: number;
  QuestionText: string;
  CorrectAnswer: string;
  RewardDigit: number;
  MoneyReward: number;
  PositionX: number;
  PositionY: number;
  level?: { LevelID: number; Name: string };
}

export interface AdminHint {
  HintID: number;
  QuestionID: number;
  HintText: string;
  Cost: number;
  HintOrder: number;
  question?: { QuestionID: number; QuestionText: string; LevelID: number };
}

export interface AdminStats {
  totalUsers: number;
  activeUsers: number;
  totalLevels: number;
  totalQuestions: number;
  totalHints: number;
  totalAnswers: number;
  correctAnswers: number;
  completedRooms: number;
}

@Injectable({ providedIn: 'root' })
export class AdminService {
  private api = `http://${window.location.hostname}:8001/api/admin`;

  constructor(private http: HttpClient) {}

  // Stats
  getStats(): Observable<AdminStats> {
    return this.http.get<AdminStats>(`${this.api}/stats`);
  }

  // Users
  getUsers(q = ''): Observable<AdminUser[]> {
    return this.http.get<AdminUser[]>(`${this.api}/users`, { params: q ? { q } : {} });
  }

  updateUser(id: number, data: Partial<AdminUser & { Password?: string }>): Observable<any> {
    return this.http.put(`${this.api}/users/${id}`, data);
  }

  deleteUser(id: number): Observable<any> {
    return this.http.delete(`${this.api}/users/${id}`);
  }

  // Levels
  getLevels(): Observable<AdminLevel[]> {
    return this.http.get<AdminLevel[]>(`${this.api}/levels`);
  }

  createLevel(data: Partial<AdminLevel>): Observable<AdminLevel> {
    return this.http.post<AdminLevel>(`${this.api}/levels`, data);
  }

  updateLevel(id: number, data: Partial<AdminLevel>): Observable<AdminLevel> {
    return this.http.put<AdminLevel>(`${this.api}/levels/${id}`, data);
  }

  deleteLevel(id: number): Observable<any> {
    return this.http.delete(`${this.api}/levels/${id}`);
  }

  // Questions
  getQuestions(levelId?: number): Observable<AdminQuestion[]> {
    const params: any = {};
    if (levelId) params.level_id = levelId;
    return this.http.get<AdminQuestion[]>(`${this.api}/questions`, { params });
  }

  createQuestion(data: Partial<AdminQuestion>): Observable<AdminQuestion> {
    return this.http.post<AdminQuestion>(`${this.api}/questions`, data);
  }

  updateQuestion(id: number, data: Partial<AdminQuestion>): Observable<AdminQuestion> {
    return this.http.put<AdminQuestion>(`${this.api}/questions/${id}`, data);
  }

  deleteQuestion(id: number): Observable<any> {
    return this.http.delete(`${this.api}/questions/${id}`);
  }

  // Hints
  getHints(questionId?: number): Observable<AdminHint[]> {
    const params: any = {};
    if (questionId) params.question_id = questionId;
    return this.http.get<AdminHint[]>(`${this.api}/hints`, { params });
  }

  createHint(data: Partial<AdminHint>): Observable<AdminHint> {
    return this.http.post<AdminHint>(`${this.api}/hints`, data);
  }

  updateHint(id: number, data: Partial<AdminHint>): Observable<AdminHint> {
    return this.http.put<AdminHint>(`${this.api}/hints/${id}`, data);
  }

  deleteHint(id: number): Observable<any> {
    return this.http.delete(`${this.api}/hints/${id}`);
  }
}
