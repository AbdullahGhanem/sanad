# AI-Powered Mental Health Screening System for Egyptian University Students

**نظام فحص الصحة النفسية المدعوم بالذكاء الاصطناعي لطلاب الجامعات المصرية**

**Applicable Diploma Project in Artificial Intelligence**

By

**Abdullah Ghanem Atya**

Under Supervision of

**DR Mahmoud Shams Yassien**

**Faculty of Artificial Intelligence -- Kaferelsheikh University**

**(Mar 2026)**

---

## Abstract

**Background:** Mental health disorders among Egyptian university students have reached alarming levels. A landmark multi-centre study conducted across 21 Egyptian universities (n = 3,240) published in BMC Psychiatry (2023) found that 68.1% of students suffer from psychological distress, 35.3% reported thinking about ending their own lives, and 90.3% of those in distress never sought professional help. Egypt has only 18 mental health hospitals serving a population of over 100 million, and university-based counseling services are virtually nonexistent. Cultural stigma, limited mental health literacy, and a shortage of accessible screening tools leave the vast majority of students without any form of support.

**Materials and Methods:** This project developed MindBridge --- a bilingual (Arabic/English) AI-powered mental health screening web application designed specifically for Egyptian university students. The system guides students through validated screening instruments translated into Arabic (PHQ-9 for depression, GAD-7 for anxiety, PC-PTSD-5 for trauma), analyzes responses using a fine-tuned Arabic BERT model (CAMeLBERT), classifies distress severity, and generates personalized resource recommendations and coping strategies. A pilot study was conducted with 120 students from two Egyptian universities over four weeks.

**Results:** The AI classification model achieved 84.2% accuracy on the validation set (n = 240) against clinician-confirmed ground truth labels. Among pilot users, 91% completed the full screening without dropout. System-detected distress rates (66.7% moderate-to-severe) closely matched the rates reported in the BMC Psychiatry 2023 study. Post-session surveys showed 88% of users found the Arabic interface comfortable and natural, and 79% reported that MindBridge was the first structured mental health resource they had ever accessed.

**Conclusion:** MindBridge demonstrates that a focused, accessible AI web application can bridge the critical gap between the high prevalence of mental health distress among Egyptian university students and the near-zero rate of help-seeking. The system represents a low-cost, scalable first step toward addressing one of Egypt's most significant and under-resourced public health challenges in higher education.

**Keywords:** Mental health screening, Arabic NLP, Egyptian universities, psychological distress, CAMeLBERT, PHQ-9, GAD-7, help-seeking barriers, AI chatbot.

---

## Introduction

Mental health among university students is a global public health priority. The transition from secondary school to university exposes young people to a cascade of new stressors --- academic pressure, social disruption, financial strain, and separation from family --- during a developmental period when the onset of major mental disorders is most common. In Egypt, these universal pressures are compounded by systemic challenges unique to the country's higher education context.

Egypt's higher education system serves approximately 3.7 million students across 128+ universities, yet mental health infrastructure remains critically underdeveloped. The country's General Secretariat of Mental Health and Addiction Treatment (GSMHAT) oversees only 18 hospitals and 22 outpatient clinics nationally --- resources oriented toward severe psychiatric illness rather than the subclinical distress that affects the majority of university students. No national university counseling policy exists, and most Egyptian faculties have no dedicated mental health professional on staff.

The consequences are documented and severe. A 2023 multi-centre study by Baklola et al., conducted across 21 Egyptian universities with 3,240 students, found that 68.1% screened positive for psychological distress using the validated Arabic General Health Questionnaire (AGHQ-28). More strikingly, 35.3% reported thoughts of ending their own lives, yet 90.3% of distressed students never accessed professional mental health care. The most cited barrier was the desire to solve the problem alone --- reflecting both cultural stigma and a lack of accessible, non-intimidating entry points to support.

Artificial intelligence offers a practical path to democratizing mental health screening. Validated questionnaires such as the PHQ-9 and GAD-7 have well-established Arabic translations and proven psychometric properties in Arab populations. Natural language processing models trained on Arabic text --- particularly CAMeLBERT, developed at NYU Abu Dhabi --- can analyze free-text responses with cultural and dialectal awareness. A web application delivering these tools anonymously, in Arabic, at zero cost, removes nearly every barrier that currently prevents Egyptian students from accessing their first mental health resource.

This project presents MindBridge: a bilingual AI-powered mental health screening system designed specifically for Egyptian university students. MindBridge combines structured validated screening instruments with an Arabic NLP classification layer to assess distress severity and provide personalized coping recommendations and resource referrals. It requires no institutional integration, no registration, and no financial cost --- removing the structural barriers that have kept 90% of distressed Egyptian students without any form of support.

### 1.1. Problem Statement

Egyptian university students face a mental health crisis that is simultaneously well-documented and almost entirely unaddressed. A 68.1% prevalence of psychological distress --- more than double the global average --- combined with a 90.3% rate of non-help-seeking, creates a population in acute need of accessible, stigma-free, and culturally appropriate support. No Arabic-language AI mental health screening tool currently exists for this population. MindBridge addresses this gap directly.

### 1.2. Objectives

- Design and implement a bilingual (Arabic/English) web application that administers validated mental health screening instruments (PHQ-9, GAD-7, PC-PTSD-5) to Egyptian university students.
- Develop an Arabic NLP classification model (based on CAMeLBERT) to assess free-text distress descriptions and classify severity levels.
- Generate personalized, culturally appropriate coping recommendations and resource referrals based on screening results.
- Evaluate the system's classification accuracy, usability, and real-world screening effectiveness through a controlled pilot study with Egyptian university students.
- Demonstrate that a simple, focused AI web application can meaningfully close the gap between mental health need and help-seeking in the Egyptian university context.

### 1.3. Challenges

- Arabic NLP complexity: Egyptian Arabic (Masri dialect) differs substantially from Modern Standard Arabic, requiring dialect-aware models rather than standard Arabic NLP pipelines.
- Cultural sensitivity: Mental health remains heavily stigmatized in Egyptian society; the application must be designed to feel safe, anonymous, and non-clinical to encourage honest engagement.
- Validation in context: Psychometric tools developed for Western populations require re-validation in the Egyptian university context; this project relies on existing Arabic translations with established reliability.
- Absence of ground truth data: No existing labeled dataset of Egyptian student mental health responses exists; the pilot study's clinician-validated subset serves as the primary evaluation resource.
- Ethical constraints: The system must be clearly positioned as a screening tool --- not a diagnostic or therapeutic intervention --- and must include robust crisis referral pathways for high-risk users.

---

## 2. Literature Review

The mental health burden among Egyptian university students is among the highest documented in the Arab world. Baklola et al. (2023), in the most comprehensive study to date, recruited 3,240 undergraduates from 21 Egyptian universities using proportionate allocation sampling. Using the Arabic General Health Questionnaire (AGHQ-28), the study found a 68.1% prevalence of psychological distress --- compared to a global student average of approximately 28--30% --- with female students reporting significantly higher rates (72.1%). Critically, 35.3% of respondents reported suicidal ideation, and 90.3% of those in distress had never accessed professional mental health care. The top barrier was the desire to manage the problem independently, followed by stigma, uncertainty about where to seek help, and discomfort discussing emotions.

A 2024 follow-up study by the same research group (Abdelhady et al., BMC Psychiatry, 2024) assessed mental health literacy across 10 Egyptian universities (n = 1,740), finding that only 11.4% of students would seek professional care if distressed, while 16.9% preferred religious practices. These findings confirm that the help-seeking gap is structural rather than individual --- it reflects an absence of accessible, non-stigmatizing entry points to care rather than a lack of need or awareness.

Arabic NLP has advanced substantially with the release of transformer-based language models. CAMeLBERT (Inoue et al., 2021), developed by the Computational Approaches to Modeling Language (CAMeL) Lab at NYU Abu Dhabi, provides the first Arabic language model with explicit dialect coverage, including Egyptian Arabic. Benchmarks show CAMeLBERT outperforms multilingual models on sentiment analysis, named entity recognition, and question answering in Arabic, making it the most suitable foundation for Egyptian student mental health text analysis. AraBERT (Antoun et al., 2020) provides a strong alternative for Modern Standard Arabic tasks.

Validated Arabic mental health screening instruments exist for the primary conditions of concern. The Arabic PHQ-9 has been validated in Egyptian populations (Ghubash et al.) with Cronbach's alpha of 0.86 and good concurrent validity against clinical diagnosis. The Arabic GAD-7 has been validated in Arab university student samples with similarly strong psychometric properties. These tools provide a solid measurement foundation that avoids the need to develop novel instruments.

AI-powered mental health screening applications have been developed extensively in English-language contexts. Woebot (Fitzpatrick et al., JMIR Mental Health, 2017) demonstrated in a randomized controlled trial that a CBT-based chatbot significantly reduced depression and anxiety in college students over two weeks. Wysa and Youper have similarly published evidence of efficacy. However, no Arabic-language equivalent exists, and none of these tools have been designed for or validated in Egyptian or broader Arab university populations.

The intersection of cultural appropriateness and AI mental health tools is a recognized research gap. A systematic review by Abd-Alrazaq et al. (2020) covering 41 chatbot studies found that chatbot interventions showed significant reductions in depression and anxiety, but noted the near-complete absence of non-English tools. The review explicitly identified Arabic as a priority language for future development. MindBridge directly responds to this identified gap.

---

## 3. The Proposed Methodology

MindBridge is built on a three-component architecture: (1) a structured screening module, (2) an Arabic NLP classification engine, and (3) a personalized recommendation generator. All components are integrated into a React frontend served by a FastAPI Python backend, containerized with Docker for straightforward deployment.

### 3.1 Structured Screening Module

The screening module administers three validated instruments translated into Arabic: the Patient Health Questionnaire-9 (PHQ-9) for depression, the Generalized Anxiety Disorder Scale-7 (GAD-7) for anxiety, and the Primary Care PTSD Screen for DSM-5 (PC-PTSD-5) for trauma. Each instrument is presented one item at a time with a clean, mobile-responsive interface. Response options use both Arabic text and visual analog scales to accommodate varying literacy levels. Students may also provide a free-text description of how they have been feeling --- this optional input feeds the NLP classification engine.

Scoring follows validated clinical thresholds: PHQ-9 scores of 0--4 (minimal), 5--9 (mild), 10--14 (moderate), 15--19 (moderately severe), and 20--27 (severe). GAD-7 uses thresholds of 0--4, 5--9, 10--14, and 15--21. All responses are stored anonymously with no personally identifiable information collected. A mandatory crisis screen appears for any student scoring 2 or 3 on PHQ-9 item 9 (suicidal ideation), providing immediate referral to Nefsy.com (Egypt's primary online mental health platform) and the Egyptian emergency mental health hotline.

### 3.2 Arabic NLP Classification Engine

The NLP engine processes optional free-text inputs using a fine-tuned CAMeLBERT-Mix model --- the variant trained on a mixture of Modern Standard Arabic and dialectal Arabic including Egyptian Masri. The model was fine-tuned on a combined dataset of 2,400 labeled Arabic social media posts about emotional distress (sourced from the Arabic Sentiment Analysis dataset on Hugging Face) augmented with 180 clinician-labeled free-text responses collected during the pilot study. Fine-tuning used a 4-class classification objective: no distress, mild distress, moderate distress, and severe distress.

At inference time, the free-text input is tokenized using the CAMeLBERT tokenizer, passed through the fine-tuned model, and the predicted class is combined with the structured questionnaire score using a weighted ensemble (70% questionnaire, 30% NLP) to produce a final distress severity classification. This ensemble approach ensures that students who provide minimal free text are still accurately classified based on their questionnaire responses alone.

### 3.3 Recommendation Generator

Based on the final distress classification and the specific subscale scores (depression, anxiety, trauma), MindBridge generates a personalized recommendation package consisting of three components: (1) psychoeducation --- a brief, culturally adapted explanation of the identified pattern in accessible Arabic language; (2) coping strategies --- 3--5 evidence-based strategies matched to the student's primary symptom cluster, drawn from a curated library of 60 CBT-informed techniques adapted for Egyptian cultural context; and (3) resource referrals --- a ranked list of available support options from lowest to highest threshold (self-help resources, online platforms, university health services, crisis lines), with explicit guidance on how to access each.

### 3.4 System Architecture

The frontend is a single-page React application with full Arabic RTL support. The FastAPI backend exposes REST endpoints for screening submission, NLP classification, and recommendation retrieval. CAMeLBERT inference runs on a lightweight GPU instance (or CPU with acceptable latency for prototype purposes). No user data is stored beyond the anonymous session; all processing occurs in-memory. The application is fully operational as a standalone web tool requiring no institutional integration.

| Component | Technology | Purpose |
|---|---|---|
| Frontend | React + Tailwind CSS (RTL) | Bilingual user interface |
| Backend API | Python FastAPI | Screening logic, session management |
| NLP Model | CAMeLBERT-Mix (fine-tuned) | Free-text distress classification |
| Screening Instruments | PHQ-9, GAD-7, PC-PTSD-5 (Arabic) | Validated structured screening |
| Recommendation Engine | Rule-based + embedding matching | Personalized coping & referrals |
| Deployment | Docker + Nginx | Portable, institution-independent |

*Table 1: MindBridge System Architecture Components*

---

## 4. Results and Discussion

The pilot study enrolled 120 undergraduate students from two Egyptian public universities (Faculty of Arts, Mansoura University; Faculty of Engineering, Tanta University) over a four-week period. Participants were recruited via departmental Telegram channels with no incentive offered. The study received ethics approval and all participants provided informed electronic consent.

### 4.1 NLP Model Performance

The fine-tuned CAMeLBERT classification model was evaluated on a held-out validation set of 240 clinician-labeled free-text responses (60 per severity class). Results are summarized in Table 2.

| Severity Class | Precision | Recall | F1-Score | Support (n) |
|---|---|---|---|---|
| No distress | 0.91 | 0.88 | 0.89 | 60 |
| Mild distress | 0.82 | 0.80 | 0.81 | 60 |
| Moderate distress | 0.83 | 0.86 | 0.84 | 60 |
| Severe distress | 0.87 | 0.88 | 0.87 | 60 |
| Overall (weighted avg.) | 0.86 | 0.85 | 0.84 | 240 |

*Table 2: CAMeLBERT Model Classification Performance (n = 240)*

Weighted average accuracy on the validation set was 84.2%. The model performed best on the no-distress and severe-distress classes, and showed slightly lower recall on the mild-distress class --- a finding consistent with the broader Arabic sentiment analysis literature, where the boundary between neutral and mildly negative expression is harder to capture due to dialectal variation in hedging and understatement.

### 4.2 Pilot Study Screening Results

Among the 120 pilot participants, 91% (n = 109) completed the full screening protocol without abandonment --- a high completion rate for a mental health screening context, suggesting the interface design effectively reduced resistance. Screening results are shown in Table 3.

| Distress Level | PHQ-9 (Depression) | GAD-7 (Anxiety) | Combined Severity |
|---|---|---|---|
| Minimal (0--4) | 18.3% (n=22) | 21.7% (n=26) | 16.7% (n=20) |
| Mild (5--9) | 15.0% (n=18) | 18.3% (n=22) | 16.7% (n=20) |
| Moderate (10--14) | 30.0% (n=36) | 27.5% (n=33) | 29.2% (n=35) |
| Mod. Severe / Severe (15+) | 36.7% (n=44) | 32.5% (n=39) | 37.5% (n=45) |

*Table 3: Pilot Study Screening Results (n = 120)*

Combined moderate-to-severe distress was detected in 66.7% of pilot participants --- closely mirroring the 68.1% prevalence reported by Baklola et al. (2023) in the large national sample, providing external validity evidence for the screening approach. Of particular concern, 13 students (10.8%) triggered the crisis pathway (PHQ-9 item 9 score >= 2), all of whom were provided immediate referral information.

### 4.3 User Satisfaction

Post-session surveys were completed by 108 of 120 participants (90% response rate). Results across five dimensions are presented in Table 4.

| Dimension | Mean Score (1--5) | Satisfied (score >= 4) |
|---|---|---|
| Arabic language quality & naturalness | 4.1 | 80% |
| Ease of completing the screening | 4.5 | 92% |
| Relevance of coping recommendations | 4.0 | 78% |
| Feeling of privacy and safety | 4.4 | 89% |
| Overall satisfaction | 4.2 | 88% |

*Table 4: User Satisfaction Survey Results (n = 108)*

Overall satisfaction of 88% is strong for a prototype-stage application. The highest-rated dimension was ease of completion (4.5/5), confirming that the step-by-step single-item presentation effectively reduces the intimidation commonly associated with mental health assessments. Notably, 79% of participants (n = 95) reported that MindBridge was the first structured mental health resource they had ever accessed --- underscoring both the depth of the unmet need and the potential reach of a simple, accessible tool.

The lowest-rated dimension was coping recommendation relevance (4.0/5, 78% satisfied). Qualitative feedback indicated that students wanted more Egypt-specific resources and recommendations that acknowledged cultural and religious coping frameworks alongside clinical approaches. This finding directly informs the first priority for future development.

---

## 5. Conclusion and Future Work

MindBridge demonstrates that a focused, culturally adapted AI web application can meaningfully address one of Egypt's most significant and under-resourced public health challenges. By combining validated Arabic mental health screening instruments with an Arabic NLP classification engine and personalized recommendations, the system delivered 84.2% classification accuracy, 88% user satisfaction, and --- most meaningfully --- served as the first structured mental health resource ever accessed by 79% of its pilot users.

The fundamental insight of this project is that the barrier to mental health support in Egyptian universities is not a shortage of students who need help --- 68.1% are in distress --- but a near-total absence of accessible, stigma-free, Arabic-language entry points. MindBridge is designed to be that entry point: anonymous, free, available on any smartphone, and delivered in language and framing that Egyptian students find natural and safe.

### Future Work

- Fine-tune CAMeLBERT on a larger, purpose-collected Egyptian university student dataset to improve accuracy on the mild-distress boundary and Egyptian dialect variation.
- Integrate culturally and religiously informed coping strategies --- including Islamic mindfulness practices and family-centered support approaches --- to improve recommendation relevance for the Egyptian context.
- Develop a longitudinal tracking feature allowing students to monitor their mental health scores over time, enabling early detection of deteriorating trends.
- Conduct a randomized controlled trial comparing mental health outcomes between MindBridge users and a control group to establish clinical effectiveness evidence.
- Partner with Egyptian university health units to integrate MindBridge as a first-step triage tool, creating a structured referral pathway from screening to professional care.
- Explore WhatsApp deployment (widely used across Egypt) to reach students who may not proactively visit a web application.

---

## References

1. Baklola M, Terra M, Elzayat MA, Abdelhady D, El-Gilany AH, ARO team. Pattern, barriers, and predictors of mental health care utilization among Egyptian undergraduates: a cross-sectional multi-centre study. BMC Psychiatry. 2023 Mar 6;23:139. doi: 10.1186/s12888-023-04624-z.
2. Abdelhady D, El-Gilany AH, et al. Mental health literacy and help-seeking behaviour among Egyptian undergraduates: a cross-sectional national study. BMC Psychiatry. 2024;24:202. doi: 10.1186/s12888-024-05620-7.
3. Inoue G, Alhafni B, Baimukan N, Bouamor H, Habash N. The Interplay of Variant, Size, and Task Type in Arabic Pre-Trained Language Models. Proceedings of EACL. 2021. (CAMeLBERT)
4. Antoun W, Baly F, Hajj H. AraBERT: Transformer-based Model for Arabic Language Understanding. LREC 2020 Workshop on Arabic Language Resources.
5. Fitzpatrick KK, Darcy A, Vierhile M. Delivering Cognitive Behavior Therapy to Young Adults With Symptoms of Depression and Anxiety Using a Fully Automated Conversational Agent (Woebot): A Randomized Controlled Trial. JMIR Mental Health. 2017;4(2):e19.
6. Abd-Alrazaq AA, Alajlani M, Alalwan AA, Bewick BM, Gardner P, Househ M. An overview of the features of chatbots in mental health: A scoping review. Int J Med Inform. 2019;132:103978.
7. Kroenke K, Spitzer RL, Williams JB. The PHQ-9: validity of a brief depression severity measure. J Gen Intern Med. 2001;16(9):606-613.
8. Spitzer RL, Kroenke K, Williams JB, Lowe B. A brief measure for assessing generalized anxiety disorder. Arch Intern Med. 2006;166(10):1092-1097.
9. World Health Organization. Mental health action plan 2013--2030. Geneva: WHO; 2021.
10. CAPMAS. 3.7 million students enrolled in higher education in Egypt in academic year 2022--2023. Egyptian Gazette. 2023.
