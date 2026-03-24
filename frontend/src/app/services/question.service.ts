import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Question, CheckAnswerRequest, CheckAnswerResponse } from '../models/question.model';

@Injectable({ providedIn: 'root' })
export class QuestionService {
  private api = `http://${window.location.hostname}:8001/api`;

  constructor(private http: HttpClient) {}

  getQuestions(levelId: number): Observable<Question[]> {
    return this.http.get<Question[]>(`${this.api}/levels/${levelId}/questions`);
  }

  checkAnswer(questionId: number, body: CheckAnswerRequest): Observable<CheckAnswerResponse> {
    return this.http.post<CheckAnswerResponse>(`${this.api}/questions/${questionId}/check-answer`, body);
  }
}
