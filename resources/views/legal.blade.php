@extends('layout')

@section('title', $heading)

@section('content')
    <section class="mx-auto max-w-screen-2xl py-12 sm:py-16">
        <p class="text-xs font-bold uppercase tracking-widest text-hive">
            HivePaste information
        </p>

        <h1 class="mt-4 text-4xl font-bold">
            {{ $heading }}<span class="text-hive">.</span>
        </h1>

        <p class="mt-4 text-sm text-zinc-400">
            Last updated: {{ config('hivepaste.legal_updated', '9 October 2026') }}
        </p>

        <div class="mt-8 space-y-8 rounded-2xl border border-hive-border bg-hive-surface p-5 text-sm leading-7 text-zinc-300 sm:p-8">

            @if ($type === 'privacy')
                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">1. Introduction</h2>

                    <p>
                        HivePaste is a paste-sharing service provided as part of the HiveSoftware
                        ecosystem. This Privacy Policy explains what information may be collected,
                        how it is used, and how you can contact us about your privacy.
                    </p>

                    <p class="mt-3">
                        By using HivePaste, you acknowledge that information you submit may be
                        processed as described in this policy.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">2. Information We Process</h2>

                    <p>Depending on how you use HivePaste, we may process:</p>

                    <ul class="mt-3 list-disc space-y-2 pl-6">
                        <li>Paste content, titles, selected languages and expiration settings.</li>
                        <li>Paste identifiers and private management credentials.</li>
                        <li>Technical information such as IP addresses, browser information and request timestamps.</li>
                        <li>Information submitted when reporting abusive content.</li>
                        <li>Security-related information needed to detect misuse or automated requests.</li>
                    </ul>

                    <p class="mt-3">
                        We do not require an account to create or view publicly accessible paste links.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">3. How We Use Information</h2>

                    <p>Information may be processed to:</p>

                    <ul class="mt-3 list-disc space-y-2 pl-6">
                        <li>Create, display and manage pastes.</li>
                        <li>Provide sharing, expiration and deletion functionality.</li>
                        <li>Protect the service against spam, abuse and malicious activity.</li>
                        <li>Investigate reports and enforce our Terms of Service.</li>
                        <li>Maintain service availability, security and performance.</li>
                        <li>Comply with applicable legal obligations.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">4. Unlisted Pastes and Sharing</h2>

                    <p>
                        Pastes created through HivePaste are unlisted by default. This means
                        they are not intentionally published in a searchable public directory.
                    </p>

                    <p class="mt-3">
                        However, <strong class="text-white">unlisted does not mean private</strong>.
                        Anyone who obtains a paste's unique URL may be able to view its contents.
                        Links can also be forwarded or shared by other people.
                    </p>

                    <p class="mt-3">
                        Do not upload passwords, authentication tokens, confidential information,
                        personal records or other sensitive material that should not be publicly accessible.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">5. Data Retention</h2>

                    <p>
                        Pastes may remain available until their selected expiration date,
                        until they are deleted using the appropriate management credentials,
                        or until they are removed by the service operator.
                    </p>

                    <p class="mt-3">
                        Technical logs, abuse reports and backup copies may be retained separately
                        where necessary for security, service operation or legal compliance.
                        Deletion of a paste may not immediately remove all copies from backups
                        or external services where its link was shared.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">6. Cookies and Local Storage</h2>

                    <p>
                        HivePaste may use essential cookies or browser storage to support
                        security, session handling and core application functionality.
                    </p>

                    <p class="mt-3">
                        We do not intentionally use advertising cookies or behavioural advertising
                        tracking as part of HivePaste.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">7. Third-Party Services</h2>

                    <p>
                        HivePaste may rely on hosting providers, infrastructure services and
                        security services to operate. These providers may process limited
                        technical information as necessary to deliver their services.
                    </p>

                    <p class="mt-3">
                        When you follow external links, including our Discord community,
                        the privacy policies of those external services apply.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">8. Your Privacy Rights</h2>

                    <p>
                        Depending on applicable data protection law, you may have rights to
                        request access to, correction of, or deletion of personal information,
                        and to object to or restrict certain processing.
                    </p>

                    <p class="mt-3">
                        Because HivePaste supports anonymous submissions, we may need information
                        demonstrating your authority over a paste before acting on certain requests.
                        Do not publish private management credentials in public Discord channels.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">9. Contact Us</h2>

                    <p>
                        For privacy enquiries or concerns about information hosted on HivePaste,
                        please contact the HiveSoftware team through our Discord community.
                    </p>

                    <a
                        href="https://hivepanel.dev/r/discord"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-4 inline-flex items-center rounded-lg bg-hive px-5 py-3 font-bold text-black transition hover:opacity-90"
                    >
                        Contact us on Discord
                    </a>
                </div>

            @elseif ($type === 'terms')
                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">1. Acceptance of Terms</h2>

                    <p>
                        By accessing or using HivePaste, you agree to these Terms of Service.
                        If you do not agree, please do not use the service.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">2. About the Service</h2>

                    <p>
                        HivePaste provides a platform for sharing text, code snippets, logs
                        and similar content through unique URLs.
                    </p>

                    <p class="mt-3">
                        The service is provided on an as-available basis. Features, limits
                        and availability may change as the platform develops.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">3. Acceptable Use</h2>

                    <p>You must not use HivePaste to:</p>

                    <ul class="mt-3 list-disc space-y-2 pl-6">
                        <li>Host or distribute unlawful content.</li>
                        <li>Distribute malware, phishing links or malicious scripts intended to harm others.</li>
                        <li>Share stolen credentials, private keys or unlawfully obtained information.</li>
                        <li>Publish personal information without appropriate authorisation.</li>
                        <li>Harass, threaten, impersonate or exploit other individuals.</li>
                        <li>Infringe copyright or other intellectual property rights.</li>
                        <li>Spam the service, bypass rate limits or interfere with its infrastructure.</li>
                        <li>Use automated systems in ways that negatively affect service availability.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">4. Your Content</h2>

                    <p>
                        You remain responsible for the content you submit and must have the
                        necessary rights or permission to share it.
                    </p>

                    <p class="mt-3">
                        You grant the service operator the limited permission necessary
                        to store, process and display your submitted content for the purpose
                        of operating HivePaste.
                    </p>

                    <p class="mt-3">
                        We do not claim ownership of your submitted content.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">5. Public Accessibility</h2>

                    <p>
                        HivePaste generates unique links for pastes. These links are unlisted,
                        but anyone with access to a link may be able to view its content.
                    </p>

                    <p class="mt-3">
                        You are responsible for deciding whether the content is suitable
                        for sharing through a link-accessible service.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">6. Content Removal</h2>

                    <p>
                        We reserve the right to remove or restrict access to content
                        that violates these Terms, applicable law or the security
                        of our infrastructure.
                    </p>

                    <p class="mt-3">
                        We may investigate reports of abuse and take action without
                        prior notice where reasonably necessary.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">7. Availability and Data Loss</h2>

                    <p>
                        We aim to keep HivePaste available and reliable, but we do not
                        guarantee uninterrupted access, permanent storage or error-free operation.
                    </p>

                    <p class="mt-3">
                        Pastes may expire, be deleted or become unavailable. You should
                        maintain your own backups of any content you wish to retain.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">8. API Usage</h2>

                    <p>
                        Where API access is provided, you must keep authentication
                        credentials secure and respect the service's rate limits
                        and usage restrictions.
                    </p>

                    <p class="mt-3">
                        We may restrict or revoke API access where necessary to prevent
                        abuse or protect service stability.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">9. Changes to These Terms</h2>

                    <p>
                        We may update these Terms as the service evolves. Changes will
                        be reflected on this page with an updated revision date.
                        Continued use following an update constitutes acceptance
                        to the extent permitted by applicable law.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">10. Contact</h2>

                    <p>
                        If you have questions about these Terms, please contact
                        the HiveSoftware team through Discord.
                    </p>

                    <a
                        href="https://hivepanel.dev/r/discord"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-4 inline-flex items-center rounded-lg bg-hive px-5 py-3 font-bold text-black transition hover:opacity-90"
                    >
                        Join our Discord
                    </a>
                </div>

            @else
                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">Report Abuse</h2>

                    <p>
                        We want HivePaste to remain a useful and safe platform for sharing
                        text and code. If you discover content that violates our Terms
                        of Service or applicable law, please report it.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">What Can Be Reported?</h2>

                    <ul class="list-disc space-y-2 pl-6">
                        <li>Malware, phishing or malicious content.</li>
                        <li>Stolen credentials or leaked sensitive information.</li>
                        <li>Harassment, threats or abusive material.</li>
                        <li>Copyright infringement.</li>
                        <li>Personal information shared without authorisation.</li>
                        <li>Other content that violates our Terms of Service.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">How to Report a Paste</h2>

                    <p>
                        If you are viewing a paste, use the
                        <strong class="text-white">Report this paste</strong>
                        button available on that page.
                    </p>

                    <p class="mt-3">
                        Alternatively, you can contact our team through Discord.
                        Please include the public paste URL and a brief explanation
                        of why you believe the content should be reviewed.
                    </p>

                    <p class="mt-3">
                        Do not share passwords, private management links or other
                        sensitive information in public Discord channels.
                    </p>

                    <a
                        href="https://hivepanel.dev/r/discord"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-4 inline-flex items-center rounded-lg bg-hive px-5 py-3 font-bold text-black transition hover:opacity-90"
                    >
                        Report via Discord
                    </a>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold text-white">What Happens Next?</h2>

                    <p>
                        Reports may be reviewed by the HiveSoftware team.
                        Where appropriate, we may remove content, restrict access
                        or take other action to protect the service and its users.
                    </p>

                    <p class="mt-3">
                        We cannot guarantee an individual response or a specific
                        resolution time for every report.
                    </p>
                </div>
            @endif

        </div>
    </section>
@endsection