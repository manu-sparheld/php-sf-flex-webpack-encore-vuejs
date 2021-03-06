import { TestBed, async } from '@angular/core/testing';
import { EditorsFormComponent } from './editors-form.component';
import {SharedModule} from "../../../shared/shared.module";
import {ApiServiceMock} from "../../../../services/api-mock";
import {ApiService} from "../../../../services/api";
import {HttpClientTestingModule} from "@angular/common/http/testing";
describe('EditorsFormComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          EditorsFormComponent
      ],
      imports: [
          HttpClientTestingModule,
          SharedModule
      ],
      providers: [
          {provide: ApiService, useClass: ApiServiceMock}
      ]
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(EditorsFormComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
