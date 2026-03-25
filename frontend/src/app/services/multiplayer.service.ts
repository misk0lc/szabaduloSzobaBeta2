import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface MultiplayerPlayer {
  UserID: number;
  Username: string;
  IsReady: boolean;
}

export interface MultiplayerSolvedQuestion {
  id: number;
  digit: number;
}

export interface MultiplayerState {
  id: number;
  LevelID: number;
  Status: 'waiting' | 'playing' | 'finished' | 'abandoned';
  SolvedQuestions: MultiplayerSolvedQuestion[];
  Players: MultiplayerPlayer[];
  MyUserID: number;
}

@Injectable({ providedIn: 'root' })
export class MultiplayerService {

  private api = `http://${window.location.hostname}:8001/api`;

  constructor(private http: HttpClient) {}

  join(levelId: number): Observable<MultiplayerState> {
    return this.http.post<MultiplayerState>(`${this.api}/multiplayer/join`, { level_id: levelId });
  }

  getState(sessionId: number): Observable<MultiplayerState> {
    return this.http.get<MultiplayerState>(`${this.api}/multiplayer/${sessionId}/state`);
  }

  solve(sessionId: number, questionId: number, rewardDigit: number): Observable<MultiplayerState> {
    return this.http.post<MultiplayerState>(`${this.api}/multiplayer/${sessionId}/solve`, {
      question_id: questionId,
      reward_digit: rewardDigit,
    });
  }

  finish(sessionId: number): Observable<void> {
    return this.http.post<void>(`${this.api}/multiplayer/${sessionId}/finish`, {});
  }

  leave(sessionId: number): Observable<void> {
    return this.http.delete<void>(`${this.api}/multiplayer/${sessionId}/leave`);
  }
}
