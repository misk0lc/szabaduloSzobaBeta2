import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class HintService {
  private api = `http://${window.location.hostname}:8001/api`;

  constructor(private http: HttpClient) {}

  use5050(): Observable<{ NewBalance: number; HintsUsed: number }> {
    return this.http.post<{ NewBalance: number; HintsUsed: number }>(`${this.api}/hints/use5050`, {});
  }
}
