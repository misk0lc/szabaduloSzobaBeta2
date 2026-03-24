import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { SubmitCodeRequest, SubmitCodeResponse } from '../models/progress.model';

@Injectable({ providedIn: 'root' })
export class ProgressService {
  private api = `http://${window.location.hostname}:8001/api`;

  constructor(private http: HttpClient) {}

  submitCode(levelId: number, body: SubmitCodeRequest): Observable<SubmitCodeResponse> {
    return this.http.post<SubmitCodeResponse>(`${this.api}/levels/${levelId}/submit-code`, body);
  }
}
