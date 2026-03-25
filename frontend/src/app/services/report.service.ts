import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface CreateReportDto {
  Title: string;
  Category?: string;
  Message: string;
  Page?: string;
}

export interface CreatePublicReportDto {
  Title: string;
  Category: string;
  ContactEmail?: string;
  Message: string;
  Page?: string;
}

@Injectable({ providedIn: 'root' })
export class ReportService {
  private api = `http://${window.location.hostname}:8001/api`;

  constructor(private http: HttpClient) {}

  createReport(data: CreateReportDto): Observable<any> {
    return this.http.post(`${this.api}/reports`, data);
  }

  createPublicReport(data: CreatePublicReportDto): Observable<any> {
    return this.http.post(`${this.api}/reports/public`, data);
  }
}
